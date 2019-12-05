<?php
/**
 *  * Created by PhpStorm.
 * User: sergey_h
 * Date: 07.02.19
 * Time: 8:57
 */

namespace App\Entity\Extra;


/**
 * Trait NewCheckable
 *
 * @package App\Entity\Extra
 */
trait NewCheckable
{
    /**
     *
     * isNew
     *
     * @return bool
     */
    public function isNew()
    {
        return null == $this->getId();
    }
}