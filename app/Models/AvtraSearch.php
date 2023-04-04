<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvtraSearch extends Model
{

    public $tableName = 'avtra_searches';
    protected $casts = ['searched_options' => 'array','pricing'=>'array','passengers'=>'array'];
}
