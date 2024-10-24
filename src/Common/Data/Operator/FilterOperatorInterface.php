<?php

namespace App\Common\Data\Operator;

use Doctrine\ORM\QueryBuilder;

interface FilterOperatorInterface
{
    public function getValueCount(): int;

    public function getLabel(): string;

    public function addFilterToQueryBuilder(QueryBuilder $qb, string $alias, string $field, array $values): void;
}
