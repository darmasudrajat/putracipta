<?php

namespace App\Common\Data\Operator;

use Doctrine\ORM\QueryBuilder;

class FilterNotNull implements FilterOperatorInterface
{
    public function getValueCount(): int
    {
        return 0;
    }

    public function getLabel(): string
    {
        return 'Is not null';
    }

    public function addFilterToQueryBuilder(QueryBuilder $qb, string $alias, string $field, array $values): void
    {
        $qb->andWhere("{$alias}.{$field} IS NOT NULL");
    }
}
