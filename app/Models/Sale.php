<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Mongodb\Eloquent\Model;

class Sale extends Model
{
  use HasFactory;
  use SoftDeletes;

  protected $connection = 'mongodb';
  protected $collection = 'sales';

  protected $fillable = ['vehicle_id', 'sale_date', 'sale_price', 'vehicle_price', 'created_by'];

  protected $dates = ['sale_date'];

  public function vehicle()
  {
    return $this->belongsTo(Vehicle::class);
  }
}
