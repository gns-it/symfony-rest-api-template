<?php
/**
 * @author Sergey Hashimov
 * Date: 03.07.19
 * Time: 8:55
 */

namespace App\Entity\Extra;

/**
 * Interface AvailableForRemovalInterface
 * @package App\Entity\Extra
 */
interface AvailableForRemovalInterface
{
    /**
     * @return bool
     */
    public function canBeDeleted():bool;
}