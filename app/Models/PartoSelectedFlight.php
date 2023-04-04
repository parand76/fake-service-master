<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartoSelectedFlight extends Model
{

    public $tableName = 'parto_selected_flights';
    public function search(){
        return $this->belongsTo(PartoFlightSearch::class,'search_flight_id');
    }

}
