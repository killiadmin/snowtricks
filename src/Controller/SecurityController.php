<?php

namespace App\Controller;

use App\Form\NewPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new LogicException('This method can be blank.');
    }

    /**
     * @Route("/forgot_password", name="forgotten_password")
     * Generates a reset token and sends an email with instructions to reset the user's password.
     *
     * @param Request $request The request object.
     * @param UserRepository $userRepository The repository for managing user entities.
     * @param TokenGeneratorInterface $tokenGenerator The token generator for generating reset tokens.
     * @param SendMailService $mail The mail service for sending emails.
     * @return Response The response object.
     * @throws NonUniqueResultException
     */
    public function forgottenPassword(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGenerator, SendMailService $mail): Response
    {
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //Check if user exist
            $user = $userRepository->findUserByPseudo($form->get('pseudo')->getData());

            if ($user) {
                // Generate a reset token for the user
                $resetToken = $tokenGenerator->generateToken();
                $user->setResetToken($resetToken);

                // Save the updated user entity
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                // Send the reset password email to the user
                $url = $this->generateUrl('reset_pass', ['token' => $resetToken],
                    UrlGeneratorInterface::ABSOLUTE_URL);

                //Send mail
                $context = compact('url', 'user');
                $mail->sendMail(
                    'noreply@snowtricks.fr',
                    $user->getEmail(),
                    'Password reset',
                    'reset_password_email',
                    $context
                );

                $this->addFlash('success', 'An email with instructions to reset your password has been sent to your email address');
                return $this->redirectToRoute('app_login');
            }

            //User is null
            $this->addFlash('error', 'A problem has occurred');
            return $this->redirectToRoute('app_login');
        }


        return $this->render('security/reset_password.html.twig', [
            'resetPasswordForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/forgot_password/{token}", name="reset_pass")
     * Resets the user's password.
     *
     * @param string $token The reset token.
     * @param Request $request The request object.
     * @param UserRepository $userRepository The repository for managing user entities.
     * @param EntityManagerInterface $entityManager The entity manager for managing entities.
     * @param UserPasswordHasherInterface $passwordHasher The password hasher for hashing passwords.
     * @return Response The response object.
     * @throws NonUniqueResultException
     */
    public function resetPass(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        //Check if token exist in database
        $user = $userRepository->findOneByResetToken($token);

        if ($user){
            $form = $this->createForm(NewPasswordType::class);

            $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    // Update the user's password
                    $user->setPassword(
                        $passwordHasher->hashPassword(
                            $user,
                            $form->get('password')->getData()
                        )
                    );

                    // Remove the reset token
                    $user->setResetToken(null);

                    // Save the updated user entity
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Your password has been reset successfully');
                    return $this->redirectToRoute('app_login');
                }

            return $this->render('security/new_password.html.twig', [
                'passForm' => $form->createView()
            ]);
        }

        $this->addFlash('error', 'Your token is not admissible' );
        return $this->redirectToRoute('app_login');
    }
}
