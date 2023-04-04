<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItoursSearch extends Model
{
    public $tableName = 'itours_searches';
    protected $casts = ['flight_result' => 'array'];
}