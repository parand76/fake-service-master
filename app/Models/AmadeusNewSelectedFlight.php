<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmadeusNewSelectedFlight extends Model
{
    protected $table = 'amadeus_new_selected_flights';

    protected $fillable = [
        'FlightId',
        'UserId',
        'SessionId',
        'SessionToken',
    ];
}
