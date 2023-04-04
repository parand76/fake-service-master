<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SepehrSelectedFlight extends Model
{

    public $tableName = 'sepehr_selected_flights';
    protected $casts = ['FlightSegment' => 'array'];
}
