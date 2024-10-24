<?php

namespace App\Common\Data\Operator;

use Doctrine\ORM\QueryBuilder;

class FilterBetween implements FilterOperatorInterface
{
    public function getValueCount(): int
    {
        return 2;
    }

    public function getLabel(): string
    {
        return 'Between';
    }

    public function addFilterToQueryBuilder(QueryBuilder $qb, string $alias, string $field, array $values): void
    {
        $qb->andWhere("{$alias}.{$field} BETWEEN :{$alias}_{$field}_0 AND :{$alias}_{$field}_1");
        $qb->setParameter("{$alias}_{$field}_0", $values[0]);
        $qb->setParameter("{$alias}_{$field}_1", $values[1]);
    }
}
