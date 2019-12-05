<?php
/**
 * @author Sergey Hashimov
 */

namespace App\Service\User\Manager\Impl;

use App\Entity\User\User;
use App\Entity\User\UserInterface;
use App\Service\User\Manager\UserManagerInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * Class UserManager
 * @package App\Service\User\Manager\Impl
 */
class UserManager implements UserManagerInterface
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->encoderFactory = $encoderFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function createUser()
    {
        $class = $this->getClass();
        $user = new $class();

        return $user;
    }

    /**
     * Deletes a user.
     * @param UserInterface $user
     */
    public function deleteUser(UserInterface $user)
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    /**
     * Finds one user by the given criteria.
     * @param array $criteria
     * @return UserInterface|object|null
     */
    public function findUserBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByEmail($email)
    {
        return $this->findUserBy(['email' => $email]);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByConfirmationToken($token)
    {
        return $this->findUserBy(['confirmationToken' => $token]);
    }

    /**
     * Returns the user's fully qualified class name.
     * @return string
     */
    public function getClass()
    {
        return User::class;
    }

    /**
     * Reloads a user.
     * @param UserInterface $user
     */
    public function reloadUser(UserInterface $user)
    {
        $this->entityManager->refresh($user);
    }

    /**
     * Updates a user.
     * @param UserInterface $user
     * @param bool $flush
     */
    public function updateUser(UserInterface $user, $flush = true)
    {
        $this->updatePassword($user);
        $this->entityManager->persist($user);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updatePassword(UserInterface $user)
    {
        $plainPassword = $user->getPlainPassword();
        if (0 === strlen($plainPassword)) {
            return;
        }
        $encoder = $this->encoderFactory->getEncoder($user);
        $hashedPassword = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();
    }

    /**
     * @return ObjectRepository
     */
    protected function getRepository()
    {
        return $this->entityManager->getRepository($this->getClass());
    }
}
