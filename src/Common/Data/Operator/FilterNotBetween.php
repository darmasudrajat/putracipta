<?php

namespace App\Common\Data\Operator;

use Doctrine\ORM\QueryBuilder;

class FilterNotBetween implements FilterOperatorInterface
{
    public function getValueCount(): int
    {
        return 2;
    }

    public function getLabel(): string
    {
        return 'Not between';
    }

    public function addFilterToQueryBuilder(QueryBuilder $qb, string $alias, string $field, array $values): void
    {
        $qb->andWhere("{$alias}.{$field} NOT BETWEEN :{$alias}_{$field}_0 AND :{$alias}_{$field}_1");
        $qb->setParameter("{$alias}_{$field}_0", $values[0]);
        $qb->setParameter("{$alias}_{$field}_1", $values[1]);
    }
}
