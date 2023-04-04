<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartoBookingData extends Model
{
    public $timestamps = false;
    
    protected $casts = ['response' => 'array'];

    public $tableName = 'parto_booking_data';
}
