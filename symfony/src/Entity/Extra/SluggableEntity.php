<?php
/**
 * Created by PhpStorm.
 * User: sergey_h
 * Date: 18.01.19
 * Time: 23:29
 */

namespace App\Entity\Extra;

use App\Entity\Extra\Groups as SerializationGroups;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait SluggableEntity
 *
 * @package App\Entity\Extra
 */
trait SluggableEntity
{
    /**
     * @var string
     * @ORM\Column(type="string")
     *
     * @Serializer\Groups(SerializationGroups::SHORT)
     * @Assert\NotNull(message="entity.constraint.not_null")
     * @Assert\Regex(pattern="/^[a-z0-9]+(?:_[a-z0-9]+)*$/", message="entity.constraint.invalid")
     */
    protected $slug;

    /**
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

}