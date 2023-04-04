<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmadeusCreateTicket extends Model
{
    public $tableName = 'amadeus_create_tickets';
    protected $casts = ['info'=>'array'];


}
