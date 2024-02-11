<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/signup", name="app_register")
     * Registers a new user.
     *
     * @param Request $request The request object.
     * @param UserPasswordHasherInterface $userPasswordHasher The user password hasher service.
     * @param UserAuthenticatorInterface $userAuthenticator The user authenticator service.
     * @param AppAuthenticator $authenticator The app authenticator service.
     * @param EntityManagerInterface $entityManager The entity manager.
     * @param TokenGeneratorInterface $tokenGenerator The token generator service.
     * @param SendMailService $mail The send mail service.
     *
     * @return Response The response object.
     */
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        AppAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        TokenGeneratorInterface $tokenGenerator,
        SendMailService $mail
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLES_USER']);
            $user->setActivated(false);
            $user->setPictureIdentifier('default_avatar.webp');
            $newToken = $tokenGenerator->generateToken();
            $user->setNewToken($newToken);

            // Encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $url = $this->generateUrl('app_activation', ['token' => $newToken],
                UrlGeneratorInterface::ABSOLUTE_URL);

            //Send mail
            $context = compact('url', 'user');
            $mail->sendMail(
                'noreply@snowtricks.fr',
                $user->getEmail(),
                'Activated your account',
                'registration/register_email.html.twig',
                $context
            );

            $this->addFlash('success', 'Registration successful! Please check your email to activate your account.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activation/{token}", name="app_activation")
     * Activate the user account with the given token.
     *
     * @param string $token The activation token.
     * @param UserRepository $userRepository The user repository.
     * @return RedirectResponse The redirect response that redirects to the login page.
     *
     * @throws NotFoundHttpException|NonUniqueResultException If no user is associated with the token.
     */
    public function activationAccount(string $token, UserRepository $userRepository): RedirectResponse
    {
        //Check if token exist in database
        $user = $userRepository->findOneByNewToken($token);

        // If no user is associated with this token
        if(!$user){
            $this->addFlash('error', 'The reset link is invalid or has expired. Please request a new one.');
            return $this->redirectToRoute('app_home');
        }

        // We delete the token
        $user->setNewToken(null);
        $user->setActivated(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'You have successfully activated your account');
        return $this->redirectToRoute('app_login');
    }
}
