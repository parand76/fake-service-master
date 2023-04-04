<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItoursValidateFlight extends Model
{
    public $tableName = 'itours_validate_flights';
    protected $casts = ['details' => 'array'];
}