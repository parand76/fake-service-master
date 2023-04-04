<?php

namespace App\Console;

use App\Models\CityNetSearch;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CityNetSearchResponse extends Command
{
    protected $signature = 'CityNetSearchResponse';

    protected $description = '';

    public function handle()
    {
        $response = DB::table('responses')->where('message', 'Supplier Response - CityNetSearchFlight - CityNet')->get();
        $insert = [];
        foreach ($response as $res) {

            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
            $body = $item['body'];
            $array = json_decode($body, true);

            if (!is_null($array) && $array['Success'] && !empty($array['Items']) && !isset($array['Status'])) {

                ///> passengers count
                $AdultCount = 0;
                $ChildCount = 0;
                $InfantCount = 0;
                $PassengersCounts = $array['Items'][0]['AirItineraryPricingInfo']['PTC_FareBreakdowns'];
                foreach ($PassengersCounts as $passenger) {
                    if ($passenger['PassengerTypeQuantity']['Code'] == "ADT") {
                        $AdultCount += $passenger['PassengerTypeQuantity']['Quantity'];
                    } else if ($passenger['PassengerTypeQuantity']['Code'] == "CHD") {
                        $ChildCount += $passenger['PassengerTypeQuantity']['Quantity'];
                    } else if ($passenger['PassengerTypeQuantity']['Code'] == "INF") {
                        $InfantCount += $passenger['PassengerTypeQuantity']['Quantity'];
                    }
                }

                ///> cabin type
                $Cabin = $array['Items'][0]['OriginDestinationInformation']['OriginDestinationOption'][0]['FlightSegment'][0]['CabinClassCode'];

                ///> Origin and Destination
                $OriginCode =  $array['Items'][0]['OriginDestinationInformation']['OriginDestinationOption'][0]['OriginLocation'];
                $DestinationCode =  $array['Items'][0]['OriginDestinationInformation']['OriginDestinationOption'][0]['DestinationLocation'];

                ///> DepartureDateTime
                $DepartureDateTime = $array['Items'][0]['OriginDestinationInformation']['OriginDestinationOption'][0]['DepartureDateTime'];


                ///> get TripType
                $TripType = "";
                $CheckTripType = $array['Items'][0]['OriginDestinationInformation']['OriginDestinationOption'];
                if (count($CheckTripType) >= 2) {
                    if ($CheckTripType[0]['OriginLocation'] == $CheckTripType[1]['DestinationLocation']) {
                        $TripType = "Return";
                    } else {
                        $TripType = "MultiCity";
                    }
                }
                if (count($CheckTripType) == 1) {
                    $TripType = "OneWay";
                }

                $insert[] = [
                    "AdultCount" => $AdultCount,
                    "ChildCount" => $ChildCount,
                    "InfantCount" => $InfantCount,
                    "Cabin" => $Cabin,
                    "OriginCode" => $OriginCode,
                    "DestinationCode" => $DestinationCode,
                    "DepartureDateTime" => $DepartureDateTime,
                    "TripType" => $TripType,
                    "response" => json_encode($array),
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now()
                ];
            }
        }
        $this->info('start inserting : ' . count($insert) . ' items');
        foreach (array_chunk($insert, 10) as $flight) {

            CityNetSearch::insert($flight);

            $this->info('done');
        }
    }
}
