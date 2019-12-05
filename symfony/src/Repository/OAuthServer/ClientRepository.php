<?php

namespace App\Repository\OAuthServer;

use App\Entity\OAuthServer\Client;
use App\Repository\SuperRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends SuperRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * @return string
     */
    function getAlias(): string
    {
        return 'client';
    }

    /**
     * @param string $clientId
     * @return Client|null
     */
    public function findByClientId(string $clientId)
    {
        if (!strpos($clientId, '_')) {
            return null;
        }
        $credentials = explode('_', $clientId);
        $client = $this->findOneBy(['id' => $credentials[0], 'randomId' => $credentials[1]]);

        return $client;
    }
}
