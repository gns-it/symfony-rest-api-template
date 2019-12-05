<?php

declare(strict_types=1);

namespace App\Exception\Security;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Class UserIsNotEnabledException
 * @package App\Exception\Security
 */
class UserIsNotEnabledException extends AccountStatusException
{

}
