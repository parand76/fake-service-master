<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetPnrDetailsResponse extends Command
{
    protected $signature = 'GetPnrDetailsResponse';

    protected $description = '';

    public function handle()

    {
        $bookResults = DB::table('itours')->where('message', 'like', '%Supplier Response - IToursGetPNRDetails%')->pluck('info')->toArray();
        $data = [];
        $resultAll = [];
        foreach ($bookResults as $resultKey => $result) {
            $data[] = json_decode($result, true);
            $result =[];
            foreach ($data as $item) {
                if (isset($item['serialize']['curlResponse'])) {
                    $item = unserialize($item['serialize']['curlResponse']);
                    $result[] = (json_decode($item['body'], true));
                } else {
                    $result = $data;
                }
            }
            // dd($resultAll);
            $resultAll[]= $result;
            // dd($resultAll);
        }
        // dd($resultAll);
        foreach($resultAll as $r){
            foreach($r as $x){
                dump($x);
            }
        }
    }
}
