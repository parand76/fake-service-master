<?php

namespace App\Console;

use App\Models\SampleTboResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class ExtracResponseTbo extends Command
{
    protected $signature = 'ExtracResponseTbo';

    protected $description = '';

    public function handle()
    {
        $response = DB::connection('mysql_response')->table('responses')->where('message', 'Supplier Response - TBOSearch')->get();

        $insert = [];
        foreach ($response as $res) {
            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
            $body = $item['body'];

            $namespace = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $body);
            $array = json_decode(json_encode(simplexml_load_string($namespace)), TRUE);

            if (empty($array['s__Body']['HotelSearchResponse']['CityId'])) {
                continue;
            }
            $cityId = $array['s__Body']['HotelSearchResponse']['CityId'];
            $nOFRoom = $array['s__Body']['HotelSearchResponse']['NoOfRoomsRequested'];
            $condition = ['cityId' => $cityId ?? null, 'NoOfRoom' => $nOFRoom ?? null];
            $insert[] = [
                'result_type' => 'TBOSearch',
                'response' => $body,
                'condition' => json_encode($condition)
            ];
        }
        $this->info('start inserting : ' . count($insert) . ' items');
        foreach (array_chunk($insert, 10) as $hotels) {

            SampleTboResult::insert($hotels);
        }
        $this->info('done');
    }
}
