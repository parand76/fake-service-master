<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SepehrFlightBooking extends Model
{

    public $tableName = 'sepehr_flight_bookings';
    protected $casts = ['Passengers' => 'array'];
}
