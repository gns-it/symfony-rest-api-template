<?php

namespace App\Repository\OAuthServer;

use App\Entity\OAuthServer\AccessToken;
use App\Repository\SuperRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AccessToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessToken[]    findAll()
 * @method AccessToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessTokenRepository extends SuperRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessToken::class);
    }

    /**
     * @return string
     */
    function getAlias(): string
    {
        return 'at';
    }
}
