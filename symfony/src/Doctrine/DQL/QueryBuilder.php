<?php
/**
 * @author Sergey Hashimov <hashimov.sergey@gmail.com>
 */

namespace App\Doctrine\DQL;

use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder as BaseQueryBuilder;

/**
 * Class QueryBuilder
 * @package App\Doctrine\DQL
 */
class QueryBuilder extends BaseQueryBuilder
{
    /**
     * @param string $expr
     * @param null $param
     * @param callable|null $formatter
     * @return self
     */
    public function andWhereCoalesce(string $expr, $param = null, callable $formatter = null):QueryBuilder
    {
        if ($param) {
            preg_match('/:([a-zA-Z0-9_]+)/', $expr, $matches);
            if (isset($matches[1])) {
                if ($formatter) {
                    $param = $formatter($param);
                }
                $this->andWhere($expr)
                    ->setParameter($matches[1], $param);
            } else {
                throw new QueryException('Unable to extract expression parameter placeholder.');
            }
        }

        return $this;
    }

    /**
     * @param string $expr
     * @param null $param
     * @return self
     */
    public function andWhereCoalesceLike(string $expr, $param = null):QueryBuilder
    {
        return $this->andWhereCoalesce(
            $expr,
            $param,
            function (string $p) {
                return "%$p%";
            }
        );
    }

    /**
     * @param null $select
     * @return self|void
     */
    public function select($select = null)
    {
        return parent::select($select);
    }

    /**
     * @param string $from
     * @param string $alias
     * @param null $indexBy
     * @return self|void
     */
    public function from($from, $alias, $indexBy = null)
    {
        return parent::from($from, $alias, $indexBy);
    }

    /**
     * @param $predicates
     * @return self|void
     */
    public function where($predicates)
    {
        return parent::where($predicates);
    }

    /**
     * @return self|void
     */
    public function orWhere()
    {
        return parent::orWhere(...func_get_args());
    }

    /**
     * @return self|void
     */
    public function andWhere()
    {
        return parent::andWhere(...func_get_args());
    }

    /**
     * @param $key
     * @param $value
     * @param null $type
     * @return self|void
     */
    public function setParameter($key, $value, $type = null)
    {
        return parent::setParameter($key, $value, $type);
    }
}