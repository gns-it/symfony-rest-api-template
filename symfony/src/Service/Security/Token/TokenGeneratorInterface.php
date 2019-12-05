<?php
/**
 * @author Sergey Hashimov
 */

namespace App\Service\Security\Token;

/**
 * Interface TokenGeneratorInterface
 * @package App\Service\Security\Token
 */
interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generateToken(): string;

}