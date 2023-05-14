<?php

namespace App\Services;

use App\Constants\Message;
use App\Repositories\SaleRepository;
use App\Repositories\VehicleRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SaleService
{
  protected $repository;
  protected $vehicleRepository;

  public function __construct(SaleRepository $repository, VehicleRepository $vehicleRepository)
  {
    $this->repository = $repository;
    $this->vehicleRepository = $vehicleRepository;
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

  public function getSalesReportPerVehicle($vehicleId)
  {
    $salesReportData = $this->repository->getSalesReportPerVehicle($vehicleId);

    $vehicle = $this->vehicleRepository->getById($vehicleId);

    if (!$vehicle) {
      throw new ModelNotFoundException(Message::VEHICLE_NOT_FOUND);
    }

    return [
      'vehicle' => $vehicle,
      'total_sale_price' => $salesReportData['total_sale_price'],
      'report_per_date' => $salesReportData['report_per_date']
    ];
  }
}
