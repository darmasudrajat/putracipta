<?php

namespace App\Common\Data\Operator;

use Doctrine\ORM\QueryBuilder;

class SortAscending implements SortOperatorInterface
{
    public function getLabel(): string
    {
        return 'Ascending';
    }

    public function addSortToQueryBuilder(QueryBuilder $qb, string $alias, string $field): void
    {
        $qb->addOrderBy("{$alias}.{$field}", 'ASC');
    }
}
