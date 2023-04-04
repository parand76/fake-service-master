<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityNetSearch extends Model
{

    public $tableName = 'city_net_searches';
    protected $casts = ['response' => 'array'];
}
