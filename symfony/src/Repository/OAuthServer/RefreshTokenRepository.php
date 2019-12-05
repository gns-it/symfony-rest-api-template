<?php

namespace App\Repository\OAuthServer;

use App\Entity\OAuthServer\RefreshToken;
use App\Repository\SuperRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RefreshToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method RefreshToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method RefreshToken[]    findAll()
 * @method RefreshToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RefreshTokenRepository extends SuperRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    /**
     * @return string
     */
    function getAlias(): string
    {
        return 'rt';
    }
}
