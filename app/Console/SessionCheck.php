<?php

namespace App\Console;

use App\Models\TboSearch;
use Carbon\Carbon;
use Illuminate\Console\Command;


class SessionCheck extends Command
{
    protected $signature = 'SessionCheck';

    protected $description = '';

    public function handle()
    {
        $sessions = TboSearch::where('expired_at','<',Carbon::now())->delete();
        // $sessions->destroy();
    }
}