<?php
/**
 *  * Created by PhpStorm.
 * User: sergey_h
 * Date: 10.12.18
 * Time: 16:06
 */

namespace App\Entity\Extra;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\Extra\Groups as SerializationGroups;
use JMS\Serializer\Annotation as Serializer;

/**
 * Trait HasCreator
 *
 * @package App\Entity\Extra
 */
trait HasCreator
{
    /**
     * @var User
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @Serializer\Groups({SerializationGroups::CREATOR})
     */
    protected $createdBy;

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }
}