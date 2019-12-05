<?php
/**
 *  * Created by PhpStorm.
 * User: sergey_h
 * Date: 28.03.19
 * Time: 16:25
 */

namespace App\Service\Security\GrantExtension;

/**
 * Class FacebookGrantExtension
 * @package App\ApiModule\Service\GrantExtension
 */
class FacebookGrantExtension extends SuperGrantExtension
{
    /**
     * @var string
     */
    const URI = 'http://facebook.com';

    /**
     * @return string
     */
    function getFieldName(): string
    {
        return 'facebookId';
    }
}