<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AppUserChecker implements UserCheckerInterface
{
    /**
     * Checks if the user's account is activated before authentication.
     *
     * @param UserInterface $user The user object to check.
     * @return void
     * @throws CustomUserMessageAuthenticationException If the user's account is not activated.
     */
    public function checkPreAuth(UserInterface $user): void
    {
        // Checking if estActivated method exists in object $user.
        if (method_exists($user, 'isActivated') && !$user->isActivated()) {
            // You can throw an exception here to block authentication
            throw new CustomUserMessageAuthenticationException(
                'Your account is not activated.'
            );
        }
    }

    /**
     * Checks if the user's account is deactivated after authentication.
     *
     * @param UserInterface $user The user object to check.
     * @return void
     * @throws CustomUserMessageAuthenticationException If the user's account is deactivated.
     */
    public function checkPostAuth(UserInterface $user): void
    {
        // Checking if isActivated method exists in $user object.
        if (method_exists($user, 'isActivated') && !$user->isActivated()) {
            // Log out the user or deny access with a personalized message
            throw new CustomUserMessageAuthenticationException(
                'Votre compte a été désactivé.'
            );
        }
    }
}
