<?php

namespace App\Entity\Extra;

use App\Entity\Extra\Groups as SerializationGroups;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Trait HasLocation
 *
 * @package App\Entity\Extra
 */
trait HasLocation
{
    /**
     * @var float
     * @Serializer\Groups({SerializationGroups::LOCATION})
     * @ORM\Column(name="location_latitude", type="decimal", precision=10, scale=8, nullable=true)
     */
    private $latitude;

    /**
     * @var float
     * @Serializer\Groups({SerializationGroups::LOCATION})
     * @ORM\Column(name="location_longitude", type="decimal", precision=11, scale=8, nullable=true)
     */
    private $longitude;

    /**
     * @Serializer\Groups({SerializationGroups::LOCATION})
     * @ORM\Column(name="location_name", type="string", length=255, nullable=true)
     */
    private $locationName;

    /**
     * @return float
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude = null): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude = null): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getLocationName()
    {
        return $this->locationName;
    }

    /**
     * @param mixed $locationName
     */
    public function setLocationName($locationName = null): void
    {
        $this->locationName = $locationName;
    }

}