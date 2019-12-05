<?php
/**
 *  * Created by PhpStorm.
 * User: sergey_h
 * Date: 28.03.19
 * Time: 12:41
 */

namespace App\Service\Security\GrantExtension;

/**
 * Class GoogleGrantExtension
 * @package App\ApiModule\Service\GrantExtension
 */
class GoogleGrantExtension extends SuperGrantExtension
{
    /**
     * @var string
     */
    const URI = 'http://google.com';

    /**
     * @return string
     */
    function getFieldName(): string
    {
        return 'googleId';
    }

}