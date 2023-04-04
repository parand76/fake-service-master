<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TboSelectedRoom extends Model
{
    protected $casts = ['hotel_info' => 'array'];
    public $tableName='tbo_selected_rooms';

   
}
