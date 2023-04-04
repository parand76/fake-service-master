<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewItoursReserve extends Model
{
    public $tableName = 'new_itours_reserves';
    protected $casts = ['passengers_info' => 'array','flight_detail' => 'array','reserver'=> 'array','passengers_baseFare'=>'array','pricing'=>'array'];
}