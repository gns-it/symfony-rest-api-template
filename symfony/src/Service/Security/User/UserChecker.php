<?php

declare(strict_types=1);

namespace App\Service\Security\User;

use App\Entity\User\User;
use App\Exception\Security\UserIsNotEnabledException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserChecker
 * @package App\Service\Security\User
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * @param UserInterface $user
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (! $user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        $exception =  new UserIsNotEnabledException('User is not enabled');
        $exception->setUser($user);

        if (!$user->isEnabled()) {
            throw $exception;
        }
    }

    /**
     * @param UserInterface $user
     */
    public function checkPostAuth(UserInterface $user): void
    {
        // TODO: Implement checkPostAuth() method.
    }
}
