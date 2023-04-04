<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AirportBuffer extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_response';
    public $tableName = 'airport_buffers';

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function airports()
    {
        return $this->belongsTo(Airport::class, 'airport_id');
    }
}
