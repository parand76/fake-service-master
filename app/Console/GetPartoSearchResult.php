<?php

namespace App\Console;

use App\Models\PartoFlightSearch;
use App\Models\SampleTboResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\PseudoTypes\True_;

class GetPartoSearchResult extends Command
{
    protected $signature = 'GetPartoSearchResult';

    protected $description = '';

    public function handle()
    {
        $response = DB::table('responses')->where('message', 'Supplier Response - PartoSearch')->get();
        $insert = [];
        foreach ($response as $res) {

            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
            $body = $item['body'];
            $array = json_decode($body, true);
            if (empty($array['PricedItineraries'])) {
                continue;
            }

            if (count($array['PricedItineraries'][0]['OriginDestinationOptions']) == 1) {
                $AirTripType = 1;
            } else {
                $AirTripType = 2;
            }

            if (count($array['PricedItineraries'][0]['OriginDestinationOptions'][0]['FlightSegments']) == 1) {
                $OriginLocationCode = $array['PricedItineraries'][0]['OriginDestinationOptions'][0]['FlightSegments'][0]['DepartureAirportLocationCode'];
                $DestinationLocationCode = $array['PricedItineraries'][0]['OriginDestinationOptions'][0]['FlightSegments'][0]['ArrivalAirportLocationCode'];
            } else {
                $OriginLocationCode = $array['PricedItineraries'][0]['OriginDestinationOptions'][0]['FlightSegments'][0]['DepartureAirportLocationCode'];
                $end = (end($array['PricedItineraries'][0]['OriginDestinationOptions'][0]['FlightSegments']));
                $DestinationLocationCode = $end['ArrivalAirportLocationCode'];
            }

            $DepartureDateTime = $array['PricedItineraries'][0]['OriginDestinationOptions'][0]['FlightSegments'][0]['DepartureDateTime'];
            $allPassInfo = [];
            $adults = 0;
            $childs = 0;
            $infants = 0;

            foreach ($array['PricedItineraries'][0]['AirItineraryPricingInfo']['PtcFareBreakdown'] as $passenger) {
                if ($passenger['PassengerTypeQuantity']['PassengerType'] == 1) {
                    $adults = $passenger['PassengerTypeQuantity']['PassengerType'];
                }
                if ($passenger['PassengerTypeQuantity']['PassengerType'] == 2) {
                    $childs = $passenger['PassengerTypeQuantity']['PassengerType'];
                }
                if ($passenger['PassengerTypeQuantity']['PassengerType'] == 3) {
                    $infants = $passenger['PassengerTypeQuantity']['PassengerType'];
                }
            }

            $insert[] = [
                'AdultCount' => $adults,
                'InfantCount' => $infants,
                'ChildCount' => $childs,
                'AirTripType' => $AirTripType,
                'OriginLocationCode' => $OriginLocationCode,
                'DestinationLocationCode' =>  $DestinationLocationCode,
                'DepartureDateTime' => $DepartureDateTime,
                'response' => $body,
            ];
        }
        $this->info('start inserting : ' . count($insert) . ' items');
        foreach (array_chunk($insert, 10) as $flight) {

            // PartoFlightSearch::insert($flight);

        $this->info('done');
        }
    }
}
