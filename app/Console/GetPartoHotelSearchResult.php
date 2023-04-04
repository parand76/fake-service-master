<?php

namespace App\Console;

use App\Models\PartoHotelSearch;
use App\Models\SampleTboResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\PseudoTypes\True_;

class GetPartoHotelSearchResult extends Command
{
    protected $signature = 'GetPartoHotelSearchResult';

    protected $description = '';

    public function handle()
    {
        // $response = DB::table('responses')->where('message', 'Supplier Response - PartoSearchHotel - Parto Hotel')->get();
        $response = DB::table('responses')->where('message', 'like','%Supplier Response - PartoAvailableRoomsHotel - Parto Hotel%')->get();
        $insert = [];
        foreach ($response as $res) {

            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
            $body = $item['body'];
            $array = json_decode($body, true);
            if (empty($array['PricedItineraries'])) {
                continue;
            }

            $CheckIn = $array['CheckIn'];
            $CheckOut = $array['CheckOut'];
            $adults = $array['PricedItineraries'][0]['Rooms'][0]['AdultCount'];
            $childs = $array['PricedItineraries'][0]['Rooms'][0]['ChildCount'];
            $ChildAges = json_encode($array['PricedItineraries'][0]['Rooms'][0]['ChildAges']);
            $HotelId = $array['PricedItineraries'][0]['HotelId'];
            $roomId = $array['PricedItineraries'][0]['Rooms'][0]['RoomId'];
            $AvailableRoom = $array['PricedItineraries'][0]['AvailableRoom'];
            $FareSourceCode = $array['PricedItineraries'][0]['FareSourceCode'];

            $insert[] = [
                'HotelId' =>  $HotelId,
                'FareSourceCode' => $FareSourceCode,
                'RoomId' => $roomId,
                'AdultCount' => $adults,
                'ChildCount' => $childs,
                'ChildAges' => $ChildAges,
                'AvailableRooms' => $AvailableRoom,
                'CheckIn' => $CheckIn,
                'CheckOut' => $CheckOut,
                'responses' => $body
            ];
        }
       
        $this->info('start inserting : ' . count($insert) . ' items');
        foreach (array_chunk($insert, 10) as $flight) {
            PartoHotelSearch::insert($flight);
        $this->info('done');
        }
    }
}