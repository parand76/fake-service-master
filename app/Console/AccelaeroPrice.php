<?php

namespace App\Console;

use App\Models\AccelaeroPrice as ModelsAccelaeroPrice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AccelaeroPrice extends Command
{
    protected $signature = 'AccelaeroPrice';

    protected $description = '';

    public function handle()
    {
        $response = DB::table('logs')->where('message', 'LIKE', '%Supplier Response - AccelaeroPriceQuoteFlight%')->get();
        $insert = [];
        foreach ($response as $res) {

            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
            $SampleId = $res->id;
            $body = $item['body'];
            $namespace = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $body);
            $array = json_decode(json_encode(simplexml_load_string($namespace)), TRUE);

            $TripType = '';
            $FlightsInfos = [];
            if (isset($array['soap__Body']['ns1__OTA_AirPriceRS']['ns1__PricedItineraries']['ns1__PricedItinerary'])) {
                $option = $array['soap__Body']['ns1__OTA_AirPriceRS']['ns1__PricedItineraries']['ns1__PricedItinerary']['ns1__AirItinerary']['ns1__OriginDestinationOptions']['ns1__OriginDestinationOption'];
                if (isset($option['ns1__FlightSegment'])) {
                    $TripType = "OneWay";
                    $DepartureDateTime = $option['ns1__FlightSegment']['@attributes']['DepartureDateTime'];
                    $OriginCode = $option['ns1__FlightSegment']['ns1__DepartureAirport']['@attributes']['LocationCode'];
                    $DestinationCode = $option['ns1__FlightSegment']['ns1__DepartureAirport']['@attributes']['LocationCode'];
                } else {
                    $DepartureDateTime = $option[0]['ns1__FlightSegment']['@attributes']['DepartureDateTime'];
                    foreach ($option as $op) {
                        $FlightsInfos[] = [
                            'ArrivalDateTime' => $op['ns1__FlightSegment']['@attributes']['ArrivalDateTime'],
                            'FlightNumber' => $op['ns1__FlightSegment']['@attributes']['FlightNumber'],
                            'RPH' => $op['ns1__FlightSegment']['@attributes']['RPH'],
                            'OriginLocationCode' => $op['ns1__FlightSegment']['ns1__DepartureAirport']['@attributes']['LocationCode'],
                            'DestinationLocationCode' => $op['ns1__FlightSegment']['ns1__ArrivalAirport']['@attributes']['LocationCode'],
                        ];
                    }
                }

                if (count($FlightsInfos) == 2) {
                    if ($FlightsInfos[0]['OriginLocationCode'] == $FlightsInfos[1]['DestinationLocationCode']) {
                        $OriginCode = $FlightsInfos[0]['OriginLocationCode'];
                        $DestinationCode = $FlightsInfos[0]['DestinationLocationCode'];
                        $TripType = "Return";
                    } else {
                        $OriginCode = $FlightsInfos[0]['OriginLocationCode'];
                        $DestinationCode = $FlightsInfos[1]['DestinationLocationCode'];
                        $TripType = "OneWay";
                    }
                }

                $FlightsInfos = [];

                $AdultCount = 0;
                $InfantCount = 0;
                $ChildCount = 0;

                $passengerInfo = $array['soap__Body']['ns1__OTA_AirPriceRS']['ns1__PricedItineraries']['ns1__PricedItinerary']['ns1__AirItineraryPricingInfo']['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown'];

                if (isset($passengerInfo['ns1__PassengerTypeQuantity'])) {
                    if ($passengerInfo['ns1__PassengerTypeQuantity']['@attributes']['Code'] == 'ADT') {
                        $AdultCount = $passengerInfo['ns1__PassengerTypeQuantity']['@attributes']['Quantity'];
                    } else if ($passengerInfo['ns1__PassengerTypeQuantity']['@attributes']['Code'] == 'CHD') {
                        $ChildCount = $passengerInfo['ns1__PassengerTypeQuantity']['@attributes']['Quantity'];;
                    } else if ($passengerInfo['ns1__PassengerTypeQuantity']['@attributes']['Code'] == 'INF') {
                        $InfantCount = $passengerInfo['ns1__PassengerTypeQuantity']['@attributes']['Quantity'];
                    }
                } else {
                    foreach ($passengerInfo as $in) {
                        if ($in['ns1__PassengerTypeQuantity']['@attributes']['Code'] == 'ADT') {
                            $AdultCount = $in['ns1__PassengerTypeQuantity']['@attributes']['Quantity'];
                        } else if ($in['ns1__PassengerTypeQuantity']['@attributes']['Code'] == 'CHD') {
                            $ChildCount = $in['ns1__PassengerTypeQuantity']['@attributes']['Quantity'];
                        } else if ($in['ns1__PassengerTypeQuantity']['@attributes']['Code'] == 'INF') {
                            $InfantCount = $in['ns1__PassengerTypeQuantity']['@attributes']['Quantity'];
                        }
                    }
                }

                $insert[] = [
                    'sample_accel_result_id' => $SampleId,
                    'AdultCount' => $AdultCount,
                    'InfantCount' => $InfantCount,
                    'ChildCount' => $ChildCount,
                    'TripType' => $TripType,
                    'DepratureDateTime' => $DepartureDateTime,
                    'OriginCode' => $OriginCode,
                    'DestinationCode' => $DestinationCode,
                    'responses' => json_encode($body)
                ];
            }
        }
        $this->info('start inserting : ' . count($insert) . ' items');
        foreach (array_chunk($insert, 10) as $hotels) {
            ModelsAccelaeroPrice::insert($hotels);
        }
        $this->info('done');
    }
}
