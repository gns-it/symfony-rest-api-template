<?php

namespace App\Entity\OAuthServer;

use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;

/**
 * Class AccessToken
 * @ORM\Entity(repositoryClass="App\Repository\OAuthServer\AccessTokenRepository")
 * @ORM\Table(name="oauth_access_token")
 */
class AccessToken extends BaseAccessToken
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

}
