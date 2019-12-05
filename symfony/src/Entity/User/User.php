<?php

namespace App\Entity\User;

use App\Entity\Extra\Groups as SerializationGroups;
use App\Entity\Extra\HasId;
use App\Entity\Extra\HasUuid;
use App\Entity\Extra\NewCheckable;
use App\Entity\Extra\TimestampableEntity;
use App\Entity\Media\Media;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\User\UserRepository")
 * @UniqueEntity(fields={"email"})
 */
class User implements UserInterface
{
    use HasId, HasUuid, NewCheckable, TimestampableEntity;

    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    const ROLE_USER = 'ROLE_USER';

    const ROLE_PROFILE_FILLED = 'ROLE_PROFILE_FILLED';

    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\Email()
     * @ORM\Column(type="string", length=180, unique=true)
     * @Serializer\Groups(SerializationGroups::DETAILED_SHORT)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Groups(SerializationGroups::PROFILE)
     */
    private $phone;

    /**
     * @var array
     * @ORM\Column(type="json")
     * @SWG\Property(property="roles", type="array", items={"type"="string"}, example={User::ROLE_USER})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    private $blockedAt;

    /**
     * @var bool
     * @Serializer\Groups(SerializationGroups::DETAILED_SHORT)
     * @ORM\Column(type="boolean")
     */
    private $confirmed;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmationToken;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $deleted;

    /**
     * @var string
     */
    private $plainPassword;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordRequestedAt;

    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="App\Entity\Media\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="avatar_id", nullable=true, onDelete="SET NULL")
     * @Serializer\Groups(SerializationGroups::DETAILED_SHORT)
     */
    private $avatar;

    public function __construct()
    {
        $this->enabled = false;
        $this->deleted = false;
        $this->confirmed = false;
        $roles[] = self::ROLE_USER;

    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param string $role
     * @return User
     */
    public function addRole(string $role): self
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param string $role
     * @return User
     */
    public function removeRole(string $role): self
    {
        $this->roles = array_values(array_diff($this->roles, [$role]));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * Gets the plain password.
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Sets the plain password.
     * @param string $password
     * @return static
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return void
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * @param bool $enabled
     * @return void
     */
    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return void
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * @return bool
     */
    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return string
     */
    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken
     */
    public function setConfirmationToken(string $confirmationToken = null): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * @return \DateTime
     */
    public function getPasswordRequestedAt(): ?\DateTime
    {
        return $this->passwordRequestedAt;
    }

    /**
     * @param \DateTime $passwordRequestedAt
     */
    public function setPasswordRequestedAt(\DateTime $passwordRequestedAt = null): void
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return Media
     */
    public function getAvatar(): ?Media
    {
        return $this->avatar;
    }

    /**
     * @param Media $avatar
     */
    public function setAvatar(Media $avatar = null): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    /**
     * @return void
     */
    public function confirm()
    {
        $this->confirmed = true;
    }

    /**
     * @param bool $confirmed
     */
    public function setConfirmed(bool $confirmed): void
    {
        $this->confirmed = $confirmed;
    }

    /**
     * @return bool
     * @Serializer\VirtualProperty()
     * @Serializer\Type("boolean")
     * @Serializer\SerializedName("isProfileFilled")
     * @Serializer\Groups(SerializationGroups::PROFILE)
     */
    public function isProfileFilled(): ?bool
    {
        return $this->hasRole(self::ROLE_PROFILE_FILLED);
    }

    /**
     * @return \DateTime
     */
    public function getBlockedAt(): \DateTime
    {
        return $this->blockedAt;
    }

    /**
     * @param \DateTime $blockedAt
     */
    public function setBlockedAt(\DateTime $blockedAt): void
    {
        $this->blockedAt = $blockedAt;
    }

    public function isBlocked()
    {
        return $this->blockedAt !== null;
    }
}
