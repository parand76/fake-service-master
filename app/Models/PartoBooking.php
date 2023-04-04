<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartoBooking extends Model
{

    public $tableName = 'parto_bookings';

    public function search()
    {
        return $this->belongsTo(PartoFlightSearch::class, 'parto_flight_search_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }
}
