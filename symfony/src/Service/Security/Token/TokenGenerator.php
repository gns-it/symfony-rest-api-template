<?php
/**
 * @author Sergey Hashimov
 */

namespace App\Service\Security\Token;

/**
 * Class TokenGenerator
 * @package App\Service\Security\Token
 */
class TokenGenerator implements TokenGeneratorInterface
{
    /**
     * @param int $length
     * @return string
     */
    public function generateToken(int $length = 32): string
    {
        return rtrim(strtr(base64_encode(random_bytes($length)), '+/', '-_'), '=');
    }
}