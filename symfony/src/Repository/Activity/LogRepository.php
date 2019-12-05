<?php

namespace App\Repository\Activity;

use App\Entity\Activity\Log;
use App\Repository\SuperRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends SuperRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    /**
     * @return string
     */
    function getAlias(): string
    {
        return 'log';
    }


    /**
     * @param Request $request
     * @return PaginationInterface
     */
    public function htmlPaginate(Request $request)
    {
        $qb = $this->queryBuilder('log');
        $qb->andWhereCoalesceLike(
            "log.context LIKE :search OR log.message LIKE :search",
            $request->query->get('search')
        );

        return $this->paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 50)
        );
    }
}
