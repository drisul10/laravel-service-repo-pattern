<?php

namespace App\Models;

class Car extends Vehicle
{
  protected $fillable = [
    'engine',
    'passenger_capacity',
    'car_type',
  ];

  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
    $this->attributes['type'] = 'car';
  }
}
