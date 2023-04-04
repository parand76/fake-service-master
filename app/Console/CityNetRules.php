<?php

namespace App\Console;

use App\Models\CityNetFlightsRule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CityNetRules extends Command
{
    protected $signature = 'CityNetRules';

    protected $description = '';

    public function handle()
    {
        $response = DB::table('responses')->where('message', 'Supplier Response - CityNetFareRulesFlight - CityNet')->get(['info']);
        $insert = [];

        foreach ($response as $res) {

            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
            $PassengerType = '';
            if (isset($item['ADT'])) {
                $PassengerType = 'ADT';
            }
            if (isset($item['CHD'])) {
                $PassengerType = 'CHD';
            }
            if (isset($item['INF'])) {
                $PassengerType = 'INF';
            }

            $body = $item[$PassengerType]['body'];
            $array = json_decode($body, true);

            if (!empty($array) && isset($array['Success']) && $array['Success'] && !empty($array['Items'])) {
                $response = $array;
                $DepartureCode = $array['Items'][0]['DepartureLocationCode'];
                $ArrivalCode = $array['Items'][0]['ArrivalLocationCode'];
                $AirLine = $array['Items'][0]['Airline'];
                $MarketAirLine = $array['Items'][0]['MarketAirline'];
            }

            $insert[]  = [
                'PassengerType' => $PassengerType,
                'DepartureLocationCode' => $DepartureCode,
                'ArrivalLocationCode' => $ArrivalCode,
                'AirLine' => $AirLine,
                'MarketAirLine' => $MarketAirLine,
                'response' => json_encode($response),
            ];
        }

        $this->info('start inserting : ' . count($insert) . ' items');

        foreach (array_chunk($insert, 10) as $flight) {

            CityNetFlightsRule::insert($flight);

            $this->info('done');
        }
    }
}
