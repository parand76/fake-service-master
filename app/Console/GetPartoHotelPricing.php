<?php

namespace App\Console;

use App\Models\PartoHotelPricing;
use App\Models\SampleTboResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\PseudoTypes\True_;

class GetPartoHotelPricing extends Command
{
    protected $signature = 'GetPartoHotelPricing';

    protected $description = '';

    public function handle()
    {
        $response = DB::table('logs')->where('message', 'Supplier Response - PartoAvailabilityAndPricingHotel - Parto Hotel')->get();
        $insert = [];
        foreach ($response as $res) {

            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
            $body = $item['body'];
            $array = json_decode($body, true);

            if (empty($array['PricedItinerary'])) {
                continue;
            }

            $CheckIn = $array['CheckIn'];
            $CheckOut = $array['CheckOut'];
            $HotelId = $array['PricedItinerary']['HotelId'];
            $AvailableRoom = $array['PricedItinerary']['AvailableRoom'];
            $fareSource = $array['PricedItinerary']['FareSourceCode'];

            $insert[] = [
                'HotelId' =>  $HotelId,
                'AvailableRooms' => $AvailableRoom,
                'FareSourceCode' => $fareSource,
                'CheckIn' => $CheckIn,
                'CheckOut' => $CheckOut,
                'responses' => $body
            ];
        }

        $this->info('start inserting : ' . count($insert) . ' items');
        foreach (array_chunk($insert, 10) as $flight) {
            PartoHotelPricing::insert($flight);
            $this->info('done');
        }
    }
}
