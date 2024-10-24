<?php

namespace App\Common\Data\Criteria;

class DataCriteria
{
    private array $filter = [];
    private array $sort = [];
    private DataCriteriaPagination $pagination;

    public function __construct()
    {
        $this->pagination = new DataCriteriaPagination();
    }

    public function getFilter(): array
    {
        return $this->filter;
    }

    public function setFilter(array $filter): void
    {
        $this->filter = $filter;
    }

    public function getSort(): array
    {
        return $this->sort;
    }

    public function setSort(array $sort): void
    {
        $this->sort = $sort;
    }

    public function getPagination(): DataCriteriaPagination
    {
        return $this->pagination;
    }

    public function setPagination(DataCriteriaPagination $pagination): void
    {
        $this->pagination = $pagination;
    }
}
