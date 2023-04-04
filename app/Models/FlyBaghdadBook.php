<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlyBaghdadBook extends Model
{
    public $tableName = 'fly_baghdad_books';
    protected $casts = ['pass_info' => 'array','fly_info' => 'array','pricing' => 'array'];

}