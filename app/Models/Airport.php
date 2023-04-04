<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Airport extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_response';
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function airportBuffers()
    {
        return $this->belongsTo(AirportBuffer::class,'airport_id');
    }
}
