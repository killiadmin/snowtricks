<?php

namespace App\Controller;

use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
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
        throw new \LogicException('This method can be blank.');
    }

    /**
     * @Route("/forgot_password", name="forgotten_password")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param TokenGeneratorInterface $tokenGenerator
     * @param MailerInterface $mailer
     * @return Response
     * @throws NonUniqueResultException|TransportExceptionInterface
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
                $mail->send(
                    'no-reply@snowtricks.fr',
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
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
     * @throws NonUniqueResultException
     */
    public function resetPass(): Response
    {
        return $this->addFlash('sucess', 'message');
    }
}
