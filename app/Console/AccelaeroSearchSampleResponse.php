<?php

namespace App\Console;

use App\Models\AccelaeroSearch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AccelaeroSearchSampleResponse extends Command
{
    protected $signature = 'AccelaeroSearchSampleResponse';

    protected $description = '';

    public function handle()
    {
        $response = DB::table('logs')->where('message', 'LIKE', '%Supplier Response - AccelaeroSearchFlight%')->get();
        $insert = [];
        foreach ($response as $res) {

            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
            $SampleId = $res->id;

            if (isset($item['body'])) {

                $body = $item['body'];

                $namespace = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $body);
                $array = json_decode(json_encode(simplexml_load_string($namespace)), TRUE);

                if (empty($array['soap__Body']['ns1__OTA_AirAvailRS']['ns1__Errors']) && isset($array['soap__Body']['ns1__OTA_AirAvailRS']['ns1__OriginDestinationInformation']['ns1__OriginDestinationOptions'])) {

                    $FlightsInfos = [];
                    $TripType = '';
                    $flightOptions = $array['soap__Body']['ns1__OTA_AirAvailRS']['ns1__OriginDestinationInformation']['ns1__OriginDestinationOptions']['ns1__OriginDestinationOption'];

                    if(isset($flightOptions['ns1__FlightSegment'])) {
                        $FlightsInfos[] = [
                            'ArrivalDateTime' => $flightOptions['ns1__FlightSegment']['@attributes']['ArrivalDateTime'],
                            'FlightNumber' => $flightOptions['ns1__FlightSegment']['@attributes']['FlightNumber'],
                            'JourneyDuration' => $flightOptions['ns1__FlightSegment']['@attributes']['JourneyDuration'],
                            'RPH' => $flightOptions['ns1__FlightSegment']['@attributes']['RPH'],
                            'OriginLocationCode' => $flightOptions['ns1__FlightSegment']['ns1__DepartureAirport']['@attributes']['LocationCode'],
                            'DestinationLocationCode' => $flightOptions['ns1__FlightSegment']['ns1__ArrivalAirport']['@attributes']['LocationCode'],
                        ];
                        $OriginCode = $flightOptions['ns1__FlightSegment']['ns1__DepartureAirport']['@attributes']['LocationCode'];
                        $DestinationCode = $flightOptions['ns1__FlightSegment']['ns1__ArrivalAirport']['@attributes']['LocationCode'];
                        $TripType = 'OneWay';
                        $RPH = $flightOptions['ns1__FlightSegment']['@attributes']['RPH'];
                        $ArrivalDateTime = $flightOptions['ns1__FlightSegment']['@attributes']['ArrivalDateTime'];
                    }
                    else {
                        foreach ($flightOptions as $option) {
                            $FlightsInfos[] = [
                                'ArrivalDateTime' => $option['ns1__FlightSegment']['@attributes']['ArrivalDateTime'],
                                'FlightNumber' => $option['ns1__FlightSegment']['@attributes']['FlightNumber'],
                                'JourneyDuration' => $option['ns1__FlightSegment']['@attributes']['JourneyDuration'],
                                'RPH' => $option['ns1__FlightSegment']['@attributes']['RPH'],
                                'OriginLocationCode' => $option['ns1__FlightSegment']['ns1__DepartureAirport']['@attributes']['LocationCode'],
                                'DestinationLocationCode' => $option['ns1__FlightSegment']['ns1__ArrivalAirport']['@attributes']['LocationCode'],
                            ];
                        }
                        $RPH = $flightOptions[0]['ns1__FlightSegment']['@attributes']['RPH'];
                        $ArrivalDateTime = $flightOptions[0]['ns1__FlightSegment']['@attributes']['ArrivalDateTime'];
                    }
                    
                    if(count($FlightsInfos) == 2) {
                        if($FlightsInfos[0]['OriginLocationCode'] == $FlightsInfos[1]['DestinationLocationCode']) {
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

                    $passengerInfo = $array['soap__Body']['ns1__OTA_AirAvailRS']['ns1__AAAirAvailRSExt']['ns1__PricedItineraries']['ns1__PricedItinerary']['ns1__AirItineraryPricingInfo']['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown'];
                    
                    if(isset($passengerInfo['ns1__PassengerTypeQuantity'])) {
                        if ($passengerInfo['ns1__PassengerTypeQuantity']['@attributes']['Code'] == 'ADT') {
                            $AdultCount = $passengerInfo['ns1__PassengerTypeQuantity']['@attributes']['Quantity'];
                        }
                        else if ($passengerInfo['ns1__PassengerTypeQuantity']['@attributes']['Code'] == 'CHD') {
                            $ChildCount = $passengerInfo['ns1__PassengerTypeQuantity']['@attributes']['Quantity'];;
                        }
                        else if($passengerInfo['ns1__PassengerTypeQuantity']['@attributes']['Code'] == 'INF') {
                            $InfantCount = $passengerInfo['ns1__PassengerTypeQuantity']['@attributes']['Quantity'];
                        }        
                    } 
                    else {
                        foreach($passengerInfo as $in) {
                            if($in['ns1__PassengerTypeQuantity']['@attributes']['Code'] == 'ADT') {
                                $AdultCount = $in['ns1__PassengerTypeQuantity']['@attributes']['Quantity'];
                            }
                            else if($in['ns1__PassengerTypeQuantity']['@attributes']['Code'] == 'CHD') {
                                $ChildCount = $in['ns1__PassengerTypeQuantity']['@attributes']['Quantity'];
                            }  
                            else if($in['ns1__PassengerTypeQuantity']['@attributes']['Code'] == 'INF') {
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
                        'RPH' => $RPH,
                        'ArrivalDateTime' => $ArrivalDateTime,
                        'DepratureDateTime' => $array['soap__Body']['ns1__OTA_AirAvailRS']['ns1__OriginDestinationInformation']['ns1__DepartureDateTime'],
                        'OriginCode' => $OriginCode,
                        'DestinationCode' => $DestinationCode,
                        'OriginDestination' => json_encode($array['soap__Body']),
                        'responses' => json_encode($body)
                    ];
                }
            }
        }
        $this->info('start inserting : ' . count($insert) . ' items');
        foreach (array_chunk($insert, 10) as $hotels) {
            AccelaeroSearch::insert($hotels);
        }
        $this->info('done');
    }
}
