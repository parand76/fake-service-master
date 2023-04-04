<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $connection = 'mysql_response';

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
