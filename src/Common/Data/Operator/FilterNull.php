<?php

namespace App\Common\Data\Operator;

use Doctrine\ORM\QueryBuilder;

class FilterNull implements FilterOperatorInterface
{
    public function getValueCount(): int
    {
        return 0;
    }

    public function getLabel(): string
    {
        return 'Is null';
    }

    public function addFilterToQueryBuilder(QueryBuilder $qb, string $alias, string $field, array $values): void
    {
        $qb->andWhere("{$alias}.{$field} IS NULL");
    }
}
