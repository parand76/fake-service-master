<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItoursFlightReserveByide extends Model
{
    public $tableName = 'itours_flight_reserve_byides';
    protected $casts = ['flight_detail' => 'array'];

}