<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlyErbilSearch extends Model
{
    public $tableName = 'fly_erbil_searches';
    protected $casts = ['search_info' => 'array','price_info'=>"array"];

}