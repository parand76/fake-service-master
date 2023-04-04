<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlyErbilBook extends Model
{
    public $tableName = 'fly_erbil_books';
    protected $casts = ['price_info' => 'array', 'passenger_info' => 'array'];
}
