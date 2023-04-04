<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartoFlightSearch extends Model
{

    public $tableName = 'parto_flight_searches';
    protected $casts = ['response' => 'array'];

    public function book()
    {
        return $this->hasMany(PartoBooking::class);
    }
}
