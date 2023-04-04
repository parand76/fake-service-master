<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SepehrFlightSearch extends Model
{

    public $tableName = 'sepehr_flight_searches';
    protected $casts = ['response' => 'array'];
}
