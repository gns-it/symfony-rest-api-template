<?php
/**
 *  * Created by PhpStorm.
 * User: sergey_h
 * Date: 22.11.18
 * Time: 10:50
 */

namespace App\Entity\Extra;

use App\Entity\Extra\Groups as SerializationGroups;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;

/**
 * Trait HasUuid
 */
trait HasUuid
{

    /**
     * @ORM\Column(type="string", unique=true, nullable=false)
     * @ORM\GeneratedValue
     * @Serializer\Groups({SerializationGroups::DETAILED, SerializationGroups::CREATOR, SerializationGroups::SHORT, SerializationGroups::UUID})
     */
    protected $uuid;

    /**
     * @return mixed
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid = null)
    {
        $this->uuid = $uuid ?? Uuid::uuid4();
    }

}