<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityNetFlightsRule extends Model
{
    public $tableName = 'city_net_flights_rules';
    protected $casts = ['response' => 'array'];
}
