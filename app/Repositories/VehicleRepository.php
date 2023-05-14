<?php

namespace App\Repositories;

interface VehicleRepository
{
  public function save(array $vehicles);

  public function getManyWithPagination($page, $perPage, $searchQuery, $filters, $sortField, $sortDirection);

  public function getById($id);
}
