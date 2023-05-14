<?php

namespace App\Repositories;

interface SaleRepository
{
  public function save(array $saleData);

  public function getManyWithPagination($page, $perPage, $searchQuery, $filters, $sortField, $sortDirection);

  public function getById($id);

  public function getSalesReportPerVehicle($vehicleId);
}
