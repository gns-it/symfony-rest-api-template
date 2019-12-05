<?php
/**
 * @author Sergey Hashimov
 */

namespace App\Entity\Extra;


use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface UserContextInterface
 * @package App\Entity\Messenger\Model
 */
interface UserContextInterface
{
    /**
     * @return UserInterface|null
     */
    public function getContext():?UserInterface;

    /**
     * @param UserInterface $user
     * @return UserInterface|null
     */
    public function setContext(UserInterface $user = null);
}