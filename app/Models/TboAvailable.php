<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TboAvailable extends Model
{
    public $timestamps = false;
    protected $casts = ['hotel_info' => 'array'];
    public $tableName='tbo_availables';

   
}
