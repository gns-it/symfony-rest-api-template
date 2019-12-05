<?php

namespace App\Entity\OAuthServer;

use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Entity\Client as BaseClient;

/**
 * Class Client
 *
 * @ORM\Entity(repositoryClass="App\Repository\OAuthServer\ClientRepository")
 * @ORM\Table(name="oauth_client")
 */
class Client extends BaseClient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
