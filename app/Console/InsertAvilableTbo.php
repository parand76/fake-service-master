<?php

namespace App\Console;

use App\Models\SampleTboResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class InsertAvilableTbo extends Command
{
    protected $signature = 'InsertAvilableTbo';

    protected $description = '';

    public function handle()
    {
        $response = DB::connection('mysql_response')->table('responses')->where('message', 'Supplier Response - TBOAvailableRooms')->get();
        $insert = [];
        foreach ($response as $res) {
            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
            $body = $item['body'];
            $insert[] = [
                'result_type' => 'TBOAvailableRooms',
                'response' => $body,
            ];
        }
        $this->info('start inserting : ' . count($insert) . ' items');
        foreach (array_chunk($insert, 10) as $hotels) {

            SampleTboResult::insert($hotels);
        }
        $this->info('done');
    }
}
