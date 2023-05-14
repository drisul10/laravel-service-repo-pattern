<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Vehicle extends Model
{
  use HasFactory;
  use SoftDeletes;

  protected $connection = 'mongodb';
  protected $collection = 'vehicles';

  protected $fillable = [
    'type',
    'name',
    'release_year',
    'color',
    'price',
    'created_by'
  ];
}
