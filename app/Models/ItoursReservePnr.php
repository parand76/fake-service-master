<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItoursReservePnr extends Model
{
    public $tableName = 'itours_reserve_pnrs';
    protected $casts = ['request' => 'array'];
}