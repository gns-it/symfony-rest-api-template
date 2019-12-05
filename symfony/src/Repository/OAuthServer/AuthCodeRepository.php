<?php

namespace App\Repository\OAuthServer;

use App\Entity\OAuthServer\AuthCode;
use App\Repository\SuperRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AuthCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthCode[]    findAll()
 * @method AuthCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthCodeRepository extends SuperRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthCode::class);
    }

    /**
     * @return string
     */
    function getAlias(): string
    {
        return 'ac';
    }
}
