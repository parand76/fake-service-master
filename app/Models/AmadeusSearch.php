<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmadeusSearch extends Model
{

    public $tableName = 'amadeus_searches';
    protected $casts = ['results' => 'array'];
}
