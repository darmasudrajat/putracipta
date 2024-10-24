<?php

namespace App\Common\Data\Operator;

use Doctrine\ORM\QueryBuilder;

class SortDescending implements SortOperatorInterface
{
    public function getLabel(): string
    {
        return 'Descending';
    }

    public function addSortToQueryBuilder(QueryBuilder $qb, string $alias, string $field): void
    {
        $qb->addOrderBy("{$alias}.{$field}", 'DESC');
    }
}
