<?php

namespace App\Common\Doctrine\Repository;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Criteria\DataCriteriaPagination;
use Doctrine\ORM\QueryBuilder;

trait EntityDataFetch
{
    public function fetchData(DataCriteria $criteria, $callback = null, string $alias = 'e'): array
    {
        $count = $this->fetchCount($criteria, $callback);
        $pagination = $criteria->getPagination();
        if ($pagination->getSize() < 1) {
            $pagination->setSize(1);
        }
        $lastPageNumber = $count === 0 ? 1 : ceil($count / $pagination->getSize());
        if ($pagination->getNumber() < 1) {
            $pagination->setNumber(1);
        } else if ($pagination->getNumber() > $lastPageNumber) {
            $pagination->setNumber($lastPageNumber);
        }
        $objects = $this->fetchObjects($criteria, $callback, $alias);

        return [$count, $objects];
    }

    public function fetchCount(DataCriteria $criteria, $callback = null, string $alias = 'e'): int
    {
        $qb = $this->createQueryBuilder($alias);

        if ($callback !== null) {
            $addList = [];
            $addList['filter'] = fn($q, $a, $f, $v) => $this->applyFilter($q, $a, $f, $v);
            $addList['sort'] = fn($q, $a, $f, $o) => $this->applySort($q, $a, $f, $o);
            $newObject = fn($e, $a) => $this->createSubQueryBuilder($e, $a);
            $callback($qb, $alias, $addList, $newObject);
        }

        $this->processDataCriteria($qb, $alias, $criteria->getFilter(), null, null);
        $qb->select("COUNT({$alias})");

        try {
            $count = $qb->getQuery()->getSingleScalarResult();
            return $count === null ? 0 : $count;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function fetchObjects(DataCriteria $criteria, $callback = null, string $alias = 'e'): array
    {
        $qb = $this->createQueryBuilder($alias);

        if ($callback !== null) {
            $addList = [];
            $addList['filter'] = fn($q, $a, $f, $v) => $this->applyFilter($q, $a, $f, $v);
            $addList['sort'] = fn($q, $a, $f, $o) => $this->applySort($q, $a, $f, $o);
            $newObject = fn($e, $a) => $this->createSubQueryBuilder($e, $a);
            $callback($qb, $alias, $addList, $newObject);
        }

        $this->processDataCriteria($qb, $alias, $criteria->getFilter(), $criteria->getSort(), $criteria->getPagination());
        return $qb->getQuery()->getResult();
    }

    private function processDataCriteria(QueryBuilder $qb, string $alias, ?array $filter, ?array $sort, ?DataCriteriaPagination $pagination): void
    {
        if (!empty($filter)) {
            foreach ($filter as $field => $values) {
                $this->applyFilter($qb, $alias, $field, $values);
            }
        }
        if (!empty($sort)) {
            foreach ($sort as $field => $sortOperator) {
                $this->applySort($qb, $alias, $field, $sortOperator);
            }
        }
        if (!empty($pagination)) {
            $pageSize = $pagination->getSize();
            $pageNumber = $pagination->getNumber();
            $qb->setMaxResults($pageSize);
            $qb->setFirstResult(($pageNumber - 1) * $pageSize);
        }
    }

    private function createSubQueryBuilder(string $entityName, string $alias): QueryBuilder
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select($alias);
        $qb->from($entityName, $alias);
        return $qb;
    }

    private function applyFilter(QueryBuilder $qb, string $alias, string $field, array $values): void
    {
        $filterOperator = $values[0];
        if (!empty($filterOperator) && !str_contains($field, ':')) {
            $filterValues = array_slice($values, 1);
            (new $filterOperator)->addFilterToQueryBuilder($qb, $alias, $field, $filterValues);
        }
    }

    private function applySort(QueryBuilder $qb, string $alias, string $field, ?string $operator): void
    {
        if (!empty($operator) && !str_contains($field, ':')) {
            (new $operator)->addSortToQueryBuilder($qb, $alias, $field);
        }
    }
}
