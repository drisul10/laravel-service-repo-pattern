<?php

namespace App\Repositories;

use App\Models\Vehicle;

class VehicleRepositoryImpl implements VehicleRepository
{
  protected $model;

  public function __construct(Vehicle $model)
  {
    $this->model = $model;
  }

  public function save(array $vehicles)
  {
    return $this->model->create($vehicles);
  }

  public function getManyWithPagination($page, $perPage, $searchQuery, $filters, $sortField, $sortDirection)
  {
    $query = $this->model->query();

    if ($searchQuery) {
      $query = $query->where('name', 'like', '%' . $searchQuery . '%');
    }

    if ($filters) {
      $filters = explode(',', $filters);
      $parsedFilters = [];
      foreach ($filters as $filter) {
        list($key, $value) = explode(':', $filter);
        $parsedFilters[$key] = $value;
      }

      foreach ($parsedFilters as $field => $value) {
        $query = $query->where($field, $value);
      }
    }

    if ($sortField) {
      $query = $query->orderBy($sortField, $sortDirection);
    }

    return $query->paginate($perPage, ['*'], 'page', $page);
  }

  public function getById($id)
  {
    return $this->model->find($id);
  }
}
