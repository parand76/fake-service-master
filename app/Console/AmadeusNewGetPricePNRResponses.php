<?php

namespace App\Console;

use App\Models\AmadeusNewPricePnr;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AmadeusNewGetPricePNRResponses extends Command
{
    protected $signature = 'AmadeusNewGetPricePNRResponses';

    protected $description = '';

    public function handle()
    {
        $response = DB::table('logs')->where('message', 'LIKE', '%Supplier Response - AmadeusPricePNRFlight - Amadeus%')->get();

        $insert = [];
        foreach ($response as $res) {

            if (isset(json_decode($res->info, true)['serialize']['curlResponse'])) {
                $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);

                $body = $item['body'];

                $Response_XML = $body;

                $namespace = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $body);
                $array = json_decode(json_encode(simplexml_load_string($namespace)), TRUE);

                if (isset($array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList'])) {

                    $Response_Json = $array;

                    $ADT = 0;
                    $CHD = 0;
                    $INF = 0;

                    if (isset($array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList'][0])) {

                        foreach ($array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList'] as $fareList) {

                            ///> origin and destination  location
                            if (isset($fareList['fareComponentDetailsGroup'][0])) {
                                $Origin = $fareList['fareComponentDetailsGroup'][0]['marketFareComponent']['boardPointDetails']['trueLocationId'];
                                if ($fareList['fareComponentDetailsGroup'][1]['marketFareComponent']['offpointDetails']['trueLocationId'] == $Origin) {
                                    ///> rounded
                                    $Destination = $fareList['fareComponentDetailsGroup'][0]['marketFareComponent']['offpointDetails']['trueLocationId'];
                                } else {
                                    ///> one way - multi segment
                                    $Destination = $fareList['fareComponentDetailsGroup'][1]['marketFareComponent']['offpointDetails']['trueLocationId'];
                                }
                            }

                            ///> count of passengers
                            if (isset($fareList['segmentInformation'][0])) {
                                if ($fareList['segmentInformation'][0]['fareQualifier']['fareBasisDetails']['discTktDesignator'] == 'ADT') {
                                    
                                    $ADT += count($fareList['paxSegReference']['refDetails']);

                                    ///> deprature and return date
                                    if (isset($fareList['segmentInformation'][0])) {
                                        $limit = count($fareList['segmentInformation']) / $ADT;

                                        $DepratureYear = substr($fareList['segmentInformation'][$limit - 1]['validityInformation'][0]['dateTime']['year'], -2);
                                        $DepratureDay = $fareList['segmentInformation'][$limit - 1]['validityInformation'][0]['dateTime']['day'];
                                        $DepratureMonth = $fareList['segmentInformation'][$limit - 1]['validityInformation'][0]['dateTime']['month'];

                                        $ReturnYear = substr($fareList['segmentInformation'][$limit]['validityInformation'][0]['dateTime']['year'], -2);
                                        $ReturnDay = $fareList['segmentInformation'][$limit]['validityInformation'][0]['dateTime']['day'];
                                        $ReturnMonth = $fareList['segmentInformation'][$limit]['validityInformation'][0]['dateTime']['month'];

                                        $ReturnDate = $ReturnDay . $ReturnMonth . $ReturnYear;
                                    } else {
                                        $DepratureYear = substr($fareList['segmentInformation']['validityInformation'][0]['dateTime']['year'], -2);
                                        $DepratureDay = $fareList['segmentInformation']['validityInformation'][0]['dateTime']['day'];
                                        $DepratureMonth = $fareList['segmentInformation']['validityInformation'][0]['dateTime']['month'];
                                        $ReturnDate = 'false';
                                    }

                                } else if ($fareList['segmentInformation'][0]['fareQualifier']['fareBasisDetails']['discTktDesignator'] == 'CH') {
                                    $CHD += count($fareList['paxSegReference']['refDetails']);
                                } else if ($fareList['segmentInformation'][0]['fareQualifier']['fareBasisDetails']['discTktDesignator'] == 'IN') {
                                    $INF += count($fareList['paxSegReference']['refDetails']);
                                }
                            } else {
                                $ADT += count($fareList['paxSegReference']['refDetails']);
                            }

                            $DepratureDate = $DepratureDay . $DepratureMonth . $DepratureYear;
                        }
                    } else {

                        ///> count of passengers
                        $ADT = count($array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['paxSegReference']['refDetails']);

                        ///> origin and destination location
                        if (isset($array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['fareComponentDetailsGroup'][0])) {
                            $Origin = $array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['fareComponentDetailsGroup'][0]['marketFareComponent']['boardPointDetails']['trueLocationId'];
                            if ($array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['fareComponentDetailsGroup'][1]['marketFareComponent']['offpointDetails']['trueLocationId'] == $Origin) {
                                ///> rounded
                                $Destination = $array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['fareComponentDetailsGroup'][0]['marketFareComponent']['offpointDetails']['trueLocationId'];
                            } else {
                                ///> one way - multi segment
                                $Destination = $array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['fareComponentDetailsGroup'][1]['marketFareComponent']['offpointDetails']['trueLocationId'];
                            }
                        } else {
                            $Origin = $array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['fareComponentDetailsGroup']['marketFareComponent']['boardPointDetails']['trueLocationId'];
                            $Destination = $array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['fareComponentDetailsGroup']['marketFareComponent']['offpointDetails']['trueLocationId'];
                        }

                        ///> deprature and return date
                        if (isset($array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['segmentInformation'][0])) {
                            $limit = count($array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['segmentInformation']) / $ADT;

                            $DepratureYear = substr($array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['segmentInformation'][$limit - 1]['validityInformation'][0]['dateTime']['year'], -2);
                            $DepratureDay = $array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['segmentInformation'][$limit - 1]['validityInformation'][0]['dateTime']['day'];
                            $DepratureMonth = $array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['segmentInformation'][$limit - 1]['validityInformation'][0]['dateTime']['month'];

                            $ReturnYear = substr($array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['segmentInformation'][$limit]['validityInformation'][0]['dateTime']['year'], -2);
                            $ReturnDay = $array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['segmentInformation'][$limit]['validityInformation'][0]['dateTime']['day'];
                            $ReturnMonth = $array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['segmentInformation'][$limit]['validityInformation'][0]['dateTime']['month'];

                            $ReturnDate = $ReturnDay . $ReturnMonth . $ReturnYear;
                        } else {
                            $DepratureYear = substr($array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['segmentInformation']['validityInformation'][0]['dateTime']['year'], -2);
                            $DepratureDay = $array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['segmentInformation']['validityInformation'][0]['dateTime']['day'];
                            $DepratureMonth = $array['soap__Body']['Fare_PricePNRWithBookingClassReply']['fareList']['segmentInformation']['validityInformation'][0]['dateTime']['month'];
                            $ReturnDate = 'false';
                        }

                        $DepratureDate = $DepratureDay . $DepratureMonth . $DepratureYear;
                    }

                    $insert[] = [
                        'ADT' => $ADT,
                        'CHD' => $CHD,
                        'INF' => $INF,
                        'Origin' => $Origin,
                        'Destination' => $Destination,
                        'DepratureDate' => $DepratureDate,
                        'ReturnDate' => $ReturnDate,
                        'Response_XML' => $Response_XML,
                        'Response_Json' => json_encode($Response_Json),
                        'created_at' => Carbon::now()
                    ];
                }
            }
        }

        $this->info('start inserting : ' . count($insert) . ' items');
        
        foreach (array_chunk($insert, 10) as $searches) {
            AmadeusNewPricePnr::insert($searches);
        }

        $this->info('done');
    }
}
