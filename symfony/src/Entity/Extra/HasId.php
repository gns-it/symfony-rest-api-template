<?php
/**
 *  * Created by PhpStorm.
 * User: sergey_h
 * Date: 22.11.18
 * Time: 10:50
 */

namespace App\Entity\Extra;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait HasId
 */
trait HasId
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

}