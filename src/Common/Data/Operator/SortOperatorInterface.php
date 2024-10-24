<?php

namespace App\Common\Data\Operator;

use Doctrine\ORM\QueryBuilder;

interface SortOperatorInterface
{
    public function getLabel(): string;

    public function addSortToQueryBuilder(QueryBuilder $qb, string $alias, string $field): void;
}
