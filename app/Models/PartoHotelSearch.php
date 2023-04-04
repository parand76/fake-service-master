<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartoHotelSearch extends Model
{

    public $tableName = 'parto_hotel_searches';
    protected $casts = ['response' => 'array'];

    // public function book()
    // {
    //     return $this->hasMany(PartoBooking::class);
    // }
}
