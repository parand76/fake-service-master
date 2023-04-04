<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleTboResult extends Model
{
    // protected $connection='mysql';
    public $timestamps = false;
    protected $casts = ['condition' => 'array'];
    public $tableName = 'sample_tbo_results';

    public function search()
    {
        return $this->hasMany(TboSearch::class,'sample_tbo_result_id');
    }
}
