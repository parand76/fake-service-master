<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;


class GetClientRefrenceId extends Command
{
    protected $signature = 'GetClientRefrenceId';

    protected $description = '';

    public function handle()
    {

        $today = Carbon::now();

        $length = 4;
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        echo  $today->isoFormat('DD') . $today->isoFormat('MM') . $today->isoFormat('YY') . $today->isoFormat('HH') . $today->isoFormat('mm') . $today->isoFormat('ss') . $today->isoFormat('SSS') . '#' . $randomString;
    }
}