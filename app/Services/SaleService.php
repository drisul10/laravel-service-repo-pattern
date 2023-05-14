<?php

namespace App\Services;

use App\Repositories\SaleRepository;

class SaleService
{
  protected $repository;

  public function __construct(SaleRepository $repository)
  {
    $this->repository = $repository;
  }

  public function save(array $salesItem)
  {
    return $this->repository->save($salesItem);
  }

  public function getManyWithPagination($page, $perPage, $searchQuery, $filters, $sortField, $sortDirection)
  {
    return $this->repository->getManyWithPagination($page, $perPage, $searchQuery, $filters, $sortField, $sortDirection);
  }

  public function getById($id)
  {
    return $this->repository->getById($id);
  }
}
