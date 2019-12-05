<?php

namespace App\Service\User;

use App\Entity\OAuthServer\AccessToken;
use App\Entity\User\User;
use App\Entity\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ProfileDestructor
 * @package App\Service\User
 */
class ProfileDestructor
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ProfileDestructor constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    /**
     * Runs destructor
     * @param UserInterface $user
     * @param bool $flush
     * @return bool
     * @throws \Exception
     */
    public function run(UserInterface $user, bool $flush = false): bool
    {
        /** @var User $user */
        $user->disable();
        $user->setDeleted(true);
        $hash = bin2hex(random_bytes(5));
        $user->setEmail("deleted.{$hash}@email.com");
        $user->setName("deleted.{$hash}@email.com");
        $user->setPhone(null);
        $user->setFacebookId(null);
        $user->setGoogleId(null);
        $user->setPasswordRequestedAt(null);
        $user->setConfirmationToken(null);
        $this->dropTokens($user);
        if ($flush) {
            $this->em->flush();
        }

        return true;
    }

    /**
     * @param User $user
     */
    public function dropTokens(User $user)
    {
        $this->em->createQuery("DELETE FROM ".AccessToken::class." t WHERE t.user = :user")->setParameter(
            'user',
            $user
        )->execute();
    }

}