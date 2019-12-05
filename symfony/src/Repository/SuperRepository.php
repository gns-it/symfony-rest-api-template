<?php
/**
 *  * Created by PhpStorm.
 * User: sergey_h
 * Date: 22.11.18
 * Time: 15:31
 */

namespace App\Repository;

use App\Doctrine\DQL\QueryBuilder as CustomQueryBuilder;
use App\Repository\Extra\CollectionPaginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class SuperRepository
 * @package App\Repository
 */
abstract class SuperRepository extends ServiceEntityRepository
{
    const FILTER_SOFT_DELETABLE = 'softdeleteable';

    /**
     * @var PaginatorInterface
     */
    public $paginator;

    /**
     * @required
     * @param PaginatorInterface $paginator
     */
    public function setPaginator(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @param string $id
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function finBySlugOrUuid(string $id)
    {
        return $this->createQueryBuilder('e')
            ->where('e.uuid = :id OR e.slug = :id')
            ->setParameter('id', $id)->getQuery()->getOneOrNullResult();
    }

    /**
     * @return string
     */
    abstract function getAlias(): string;

    /**
     * @param array $params
     * @return array
     */
    public function findByParams(array $params)
    {
        $alias = $this->getAlias();
        $qb = $this->createQueryBuilder($alias);

        return $this->paginate($qb, $params);
    }

    /**
     * @param Request $request
     * @return PaginationInterface
     */
    public function htmlPaginate(Request $request)
    {
        $alias = $this->getAlias();
        $qb = $this->createQueryBuilder($alias);

        return $this->paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 100)
        );
    }

    /**
     * @return ClassMetadata
     */
    public function getClassMetadata()
    {
        return parent::getClassMetadata();
    }

    /**
     * @param $alias
     * @param null $indexBy
     * @return CustomQueryBuilder
     */
    public function queryBuilder($alias, $indexBy = null)
    {
        return (new CustomQueryBuilder($this->_em))
            ->select($alias)
            ->from($this->_entityName, $alias, $indexBy);
    }

    /**
     * @param QueryBuilder $qb
     * @param array $params
     * @param int|null $defaultMaxLimit
     * @param bool $wrapQueries
     * @return array
     */
    public function paginate(QueryBuilder $qb, array $params, int $defaultMaxLimit = 100, bool $wrapQueries = true)
    {
        if (isset($params['pagination']) && !is_array($params['pagination'])) {
            throw new BadRequestHttpException('Value of key "pagination" must be of type array.');
        }
        $collectionPaginator = new CollectionPaginator(
            $this->paginator,
            $qb,
            $params['pagination'] ?? [],
            $defaultMaxLimit
        );

        return $collectionPaginator->paginate($wrapQueries);
    }
}