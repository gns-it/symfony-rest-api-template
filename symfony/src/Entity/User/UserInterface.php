<?php
/**
 * @author Sergey Hashimov
 */

namespace App\Entity\User;

use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

interface UserInterface extends BaseUserInterface
{
    /**
     * @param string $hashedPassword
     * @return mixed
     */
    public function setPassword(string $hashedPassword);

    /**
     * @return mixed
     */
    public function getUuid();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return string|null
     */
    public function getConfirmationToken();

    /**
     * @param string $token
     */
    public function setConfirmationToken(string $token);

    /**
     * Gets the plain password.
     *
     * @return string
     */
    public function getPlainPassword();

    /**
     * Sets the plain password.
     *
     * @param string $password
     *
     * @return static
     */
    public function setPlainPassword($password);

    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @return \DateTime
     */
    public function getPasswordRequestedAt(): ?\DateTime;

    /**
     * @param \DateTime $passwordRequestedAt
     */
    public function setPasswordRequestedAt(\DateTime $passwordRequestedAt = null): void;

    /**
     * Checks whether the password reset request has expired.
     *
     * @param int $ttl Requests older than this many seconds will be considered expired
     *
     * @return bool
     */
    public function isPasswordRequestNonExpired($ttl);

    public function isSuperAdmin():bool;

}