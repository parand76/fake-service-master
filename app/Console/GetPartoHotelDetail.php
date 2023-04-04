<?php

namespace App\Console;

use App\Models\PartoHotelBooking;
use App\Models\PartoHotelDetail;
use App\Models\SampleTboResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Faker;

class GetPartoHotelDetail extends Command
{
    protected $signature = 'GetPartoHotelDetail';

    protected $description = '';

    public function handle()
    {
        $response = DB::table('logs')->where('message', 'Supplier Response - PartoDetailHotel - Parto Hotel')->get();
        $insert = [];
        foreach ($response as $res) {

            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
            $body = $item['body'];
            $array = json_decode($body, true);
            $links = $array['Links'];

            $hotelId = 0;

            foreach ($links as $link) {
                foreach ($link as $pro) {
                    $hotelId = explode('/', $pro)[5];
                }
            }



            $insert[] = [
                "HotelId" => $hotelId,
                "responses" => $body,
            ];
        }

        $this->info('start inserting : ' . count($insert) . ' items');
        foreach (array_chunk($insert, 10) as $flight) {
            PartoHotelDetail::insert($flight);
            $this->info('done');
        }
    }
}
