<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TboSearch extends Model
{

    use SoftDeletes;
    public $timestamps = false;

    public $tableName = 'tbo_searches';
    protected $casts = ['hotel' => 'array'];

    public function sampleTbo()
    {
        return $this->belongsTo(SampleTboResult::class,'sample_tbo_result_id');
    }
}
