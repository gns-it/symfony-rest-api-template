<?php
/**
 *  * Created by PhpStorm.
 * User: sergey_h
 * Date: 27.12.18
 * Time: 11:15
 */

namespace App\Entity\Extra;


use App\Entity\Extra\Groups as SerializationGroups;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Trait TimestampableEntity
 *
 * @package App\Entity\Extra
 */
trait TimestampableEntity
{
    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     * @Serializer\Groups({SerializationGroups::DETAILED,SerializationGroups::SHORT,SerializationGroups::TIMESTAMPS})
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     * @Serializer\Groups({SerializationGroups::DETAILED,SerializationGroups::SHORT,SerializationGroups::TIMESTAMPS})
     */
    protected $updatedAt;

    /**
     * Sets createdAt.
     *
     * @param  \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Returns createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Returns createdAt.
     * @return void
     */
    public function touch()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Sets updatedAt.
     *
     * @param  \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Returns updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}