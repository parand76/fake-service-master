<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmadeusBook extends Model
{
    public $tableName = 'amadeus_books';
    public $timestamps = false;
    protected $casts = ['response'=>'array'];


}
