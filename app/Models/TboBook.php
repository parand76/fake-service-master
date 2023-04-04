<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TboBook extends Model
{
    public $timestamps = false;

    public $tableName = 'tbo_books';

    public function avilable()
    {
        return $this->hasOne(TboSelectedAvailable::class,'tbo_selected_available_id'); //> ask about true or false
    }
}
