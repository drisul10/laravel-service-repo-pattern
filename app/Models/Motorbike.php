<?php

namespace App\Models;

class Motorbike extends Vehicle
{
  protected $fillable = [
    'engine',
    'suspension_type',
    'transmission_type',
  ];

  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
    $this->attributes['type'] = 'motorbike';
  }
}
