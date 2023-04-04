<?php

namespace App\Console;

use App\Models\HotelDetail;
use App\Models\SampleTboResult;
use App\Models\TboHotelDetail as ModelsTboHotelDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class TboHotelDetail extends Command
{
    protected $signature = 'TboHotelDetail';

    protected $description = '';

    public function handle()
    {
        $response = DB::connection('mysql')->table('responses')->where('message', 'Supplier Response - TBODetail')->get();
        $insert = [];
        foreach ($response as $res) {
            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
           
            $body = $item['body'];
            $namespace = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $body);
            $array = json_decode(json_encode(simplexml_load_string($namespace)), TRUE);
            $hotelCode = $array['s__Body']['HotelDetailsResponse']['HotelDetails']['@attributes']['HotelCode'];
            $insert[] = [
                'hotel_info' => $body,
                'hotel_code' => $hotelCode
            ];
        }
        $this->info('start inserting : ' . count($insert) . ' items');
        foreach (array_chunk($insert, 10) as $hotels) {
            ModelsTboHotelDetail::insert($hotels);
        }
        $this->info('done');
    }
}
