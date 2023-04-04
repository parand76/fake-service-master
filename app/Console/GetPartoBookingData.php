<?php

namespace App\Console;

use App\Models\PartoBookingData;
use App\Models\PartoFlightSearch;
use App\Models\SampleTboResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\PseudoTypes\True_;

class GetPartoBookingData extends Command
{
    protected $signature = 'GetPartoBookingData';

    protected $description = '';

    public function handle()
    {
        $response = DB::connection('mysql_response')->table('responses')->where('message', 'Supplier Response - PartoBookingData')->get();
        $insert = [];

        foreach ($response as $res) {

            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
            $body = $item['body'];
            $array = json_decode($body, true);
            $adult = 0;
            $child = 0;
            $infant = 0;
            foreach ($array['TravelItinerary']['ItineraryInfo']['CustomerInfoes'] as $passInfo) {
                if ($passInfo['Customer']['PassengerType'] == 1) {
                    $adult++;
                }
                if ($passInfo['Customer']['PassengerType'] == 2) {
                    $child += 1;
                }
                if ($passInfo['Customer']['PassengerType'] == 3) {
                    $infant += 1;
                }
            }

            $passengerNumber = $adult . $child . $infant;
            $status = $array['Status'];
            $insert[] = [
                'passenger_number' => $passengerNumber,
                'status' => $array['Status'],
                'response' => $body,

            ];
        }

        $this->info('start inserting : ' . count($insert) . ' items');
        foreach (array_chunk($insert, 10) as $flight) {

            PartoBookingData::insert($flight);

            $this->info('done');
        }
    }
}
