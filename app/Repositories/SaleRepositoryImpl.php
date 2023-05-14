<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Models\Vehicle;

class SaleRepositoryImpl implements SaleRepository
{
  protected $model;

  public function __construct(Sale $model)
  {
    $this->model = $model;
  }

  public function save(array $salesItem)
  {
    $vehicleId = $salesItem['vehicle_id'];
    $saleDate = $salesItem['sale_date'];
    $salePrice = $salesItem['sale_price'];
    $vehicle = Vehicle::findOrFail($vehicleId);

    $sale = new Sale;
    $sale->vehicle_id = $vehicleId;
    $sale->sale_date = $saleDate;
    $sale->sale_price = $salePrice;
    $sale->vehicle_price = $vehicle->price;

    $sale->save();

    return $sale;
  }

  public function getManyWithPagination($page, $perPage, $searchQuery, $filters, $sortField, $sortDirection)
  {
    $query = $this->model->with('vehicle');

    if ($searchQuery) {
      $query = $query->where(function ($query) use ($searchQuery) {
        $query->whereHas('vehicle', function ($q) use ($searchQuery) {
          $q->where('name', 'like', '%' . $searchQuery . '%');
        });
      });
    }

    if ($filters) {
      $filters = explode(',', $filters);
      $parsedFilters = [];
      foreach ($filters as $filter) {
        list($key, $value) = explode(':', $filter);
        $parsedFilters[$key] = $value;
      }

      foreach ($parsedFilters as $field => $value) {
        if (is_numeric($value)) {
          $value = (float)$value;
        }

        if (str_starts_with($field, 'vehicle.')) {
          $vehicleField = str_replace('vehicle.', '', $field);
          $query = $query->whereHas('vehicle', function ($q) use ($vehicleField, $value) {
            $q->where($vehicleField, $value);
          });
        } else {
          $query = $query->where($field, $value);
        }
      }
    }

    if ($sortField) {
      if (str_starts_with($sortField, 'vehicle.')) {
        $vehicleField = str_replace('vehicle.', '', $sortField);
        if ($sortDirection == 'desc') {
          $collection = $query->get()->sortByDesc("vehicle.$vehicleField");
        } else {
          $collection = $query->get()->sortBy("vehicle.$vehicleField");
        }

        $total = $collection->count();
        $collection = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        $query = new \Illuminate\Pagination\LengthAwarePaginator($collection, $total, $perPage, $page, [
          'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
        ]);
      } else {
        $query = $query->orderBy($sortField, $sortDirection)->paginate($perPage, ['*'], 'page', $page);
      }
    } else {
      $query = $query->orderBy($sortField, $sortDirection)->paginate($perPage, ['*'], 'page', $page);
    }

    return $query;
  }

  public function getById($id)
  {
    return $this->model->find($id);
  }

  public function getSalesReportPerVehicle($vehicleId)
  {
    $pipeline = [
      [
        '$match' => [
          'vehicle_id' => $vehicleId
        ]
      ],
      [
        '$group' => [
          '_id' => '$sale_date',
          'total_sale_price' => [
            '$sum' => '$sale_price'
          ],
          'vehicle_id' => ['$first' => '$vehicle_id']
        ]
      ],
      [
        '$project' => [
          '_id' => 0,
          'sale_date' => '$_id',
          'total_sale_price' => 1,
        ]
      ],
      [
        '$sort' => ['sale_date' => 1]
      ]
    ];

    $reportPerDate = $this->model->raw(function ($collection) use ($pipeline) {
      return $collection->aggregate($pipeline);
    });

    $salesArray = $reportPerDate->toArray();
    $totalSalePrice = array_reduce($salesArray, function ($carry, $sale) {
      return $carry + $sale['total_sale_price'];
    }, 0);

    return [
      'report_per_date' => $reportPerDate,
      'total_sale_price' => $totalSalePrice
    ];
  }
}
