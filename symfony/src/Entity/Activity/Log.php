<?php

namespace App\Entity\Activity;

use App\Entity\Extra\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="activity_log")
 * @ORM\Entity(repositoryClass="App\Repository\Activity\LogRepository")
 */
class Log
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $context = [];

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $level;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $levelName;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $extra = [];

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message = null): void
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getContext(): ?array
    {
        dump($this->context);
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context = null): void
    {
        $this->context = $context;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     */
    public function setLevel($level = null): void
    {
        $this->level = $level;
    }

    /**
     * @return mixed
     */
    public function getLevelName()
    {
        return $this->levelName;
    }

    /**
     * @param mixed $levelName
     */
    public function setLevelName($levelName = null): void
    {
        $this->levelName = $levelName;
    }

    /**
     * @return array
     */
    public function getExtra(): ?array
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     */
    public function setExtra(array $extra = null): void
    {
        $this->extra = $extra;
    }
}
