<?php

namespace App\Services;

use App\Repositories\VehicleRepository;

class VehicleService
{
  protected $repository;

  public function __construct(VehicleRepository $repository)
  {
    $this->repository = $repository;
  }

  public function save(array $vehicles)
  {
    return $this->repository->save($vehicles);
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
