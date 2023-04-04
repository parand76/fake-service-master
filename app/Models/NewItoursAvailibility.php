<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewItoursAvailibility extends Model
{
    public $tableName = 'new_itours_availibilities';
    protected $casts = ['flight_request' => 'array'];
}