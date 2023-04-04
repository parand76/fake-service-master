<?php

namespace App\Console;

use App\Models\AmadeusNewSearch;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AmadeusNewGetSearchResponses extends Command
{
    protected $signature = 'AmadeusNewGetSearchResponses';

    protected $description = '';

    public function handle()
    {
        $response = DB::table('logs')->where('message', 'LIKE', '%Supplier Response - AmadeusSearchFlight - Amadeus%')->get();

        $insert = [];
        foreach ($response as $res) {

            if (isset(json_decode($res->info, true)['serialize']['curlResponse'])) {
                $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);

                $body = $item['body'];

                $Response_XML = $body;
                $Response_XML = preg_replace('#(.*?)<soap:Header>(.*?)</soap:Header>|</soap:Envelope>#','',$body);

                $namespace = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $body);
                $array = json_decode(json_encode(simplexml_load_string($namespace)), TRUE);

                if (isset($array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['flightIndex'])) {

                    $Response_Json = $array;

                    $Itinerary = $array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['flightIndex'];

                    ///> deparrure date - origin id - destination id
                    if (isset($array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['flightIndex'][0])) {
                        // rounded trip

                        if (isset($array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['flightIndex'][0]['groupOfFlights'][0])) {
                            $flightDetails = $array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['flightIndex'][0]['groupOfFlights'][0]['flightDetails'];
                        } else {
                            $flightDetails = $array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['flightIndex'][0]['groupOfFlights']['flightDetails'];
                        }

                        if (isset($flightDetails[0])) {
                            //multi segment
                            $Departure_Date = $flightDetails[0]['flightInformation']['productDateTime']['dateOfDeparture'];
                            $Origin_Id = $flightDetails[0]['flightInformation']['location'][0]['locationId'];
                        } else {
                            // single segment
                            $Departure_Date = $flightDetails['flightInformation']['productDateTime']['dateOfDeparture'];
                            $Origin_Id = $flightDetails['flightInformation']['location'][0]['locationId'];
                        }

                        if (isset($array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['flightIndex'][1]['groupOfFlights'][0])) {
                            $flightDetailsReturn = $array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['flightIndex'][1]['groupOfFlights'][0]['flightDetails'];
                        } else {
                            $flightDetailsReturn = $array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['flightIndex'][1]['groupOfFlights']['flightDetails'];
                        }

                        if (isset($flightDetails[0])) {
                            $Return_Date = $flightDetailsReturn[0]['flightInformation']['productDateTime']['dateOfDeparture'];
                            $Destination_Id = $flightDetailsReturn[0]['flightInformation']['location'][0]['locationId'];
                        } else {
                            $Return_Date = $flightDetailsReturn['flightInformation']['productDateTime']['dateOfDeparture'];
                            $Destination_Id = $flightDetailsReturn['flightInformation']['location'][0]['locationId'];
                        }
                    } else {
                        // one way trip
                        $Return_Date = "false";
                        if (isset($array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['flightIndex']['groupOfFlights'][0])) {
                            $flightDetails = $array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['flightIndex']['groupOfFlights'][0]['flightDetails'];
                        } else {
                            $flightDetails = $array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['flightIndex']['groupOfFlights']['flightDetails'];
                        }

                        if (isset($flightDetails[0])) {
                            //multi segment
                            $Departure_Date = $flightDetails[0]['flightInformation']['productDateTime']['dateOfDeparture'];
                            $Origin_Id = $flightDetails[0]['flightInformation']['location'][0]['locationId'];
                            $Destination_Id = $flightDetails[0]['flightInformation']['location'][1]['locationId'];
                        } else {
                            // single segment
                            $Departure_Date = $flightDetails['flightInformation']['productDateTime']['dateOfDeparture'];
                            $Origin_Id = $flightDetails['flightInformation']['location'][0]['locationId'];
                            $Destination_Id = $flightDetails['flightInformation']['location'][1]['locationId'];
                        }
                    }

                    ///> all recommendations
                    $Recommendation = $array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['recommendation'];

                    if (isset($array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['recommendation'][0])) {
                        $paxs = $array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['recommendation'][0]['paxFareProduct'];
                    } else {
                        $paxs = $array['soap__Body']['Fare_MasterPricerTravelBoardSearchReply']['recommendation']['paxFareProduct'];
                    }

                    $Adt_Count = 0;
                    $CHD_Count = 0;
                    $INF_Count = 0;
                    ///> travellers count
                    if (isset($paxs[0])) {
                        foreach ($paxs as $pax) {
                            if ($pax['paxReference']['ptc'] == 'ADT') {
                                $Adt_Count = count($pax['paxReference']['traveller']);
                            }
                            if ($pax['paxReference']['ptc'] == 'CH') {
                                $CHD_Count = count($pax['paxReference']['traveller']);
                            }
                            if ($pax['paxReference']['ptc'] == 'INF') {
                                $INF_Count = count($pax['paxReference']['traveller']);
                            }
                        }
                    } else {
                        if ($paxs['paxReference']['ptc'] == 'ADT') {
                            $Adt_Count = count($paxs['paxReference']['traveller']);
                        }
                        if ($paxs['paxReference']['ptc'] == 'CH') {
                            $CHD_Count = count($paxs['paxReference']['traveller']);
                        }
                        if ($paxs['paxReference']['ptc'] == 'INF') {
                            $INF_Count = count($paxs['paxReference']['traveller']);
                        }
                    }

                    $insert[] = [
                        'ADT' => $Adt_Count,
                        'CHD' => $CHD_Count,
                        'INF' => $INF_Count,
                        'OriginLocation' => $Origin_Id,
                        'DestinationLocation' => $Destination_Id,
                        'DepratureDate' => $Departure_Date,
                        'ReturnDate' => $Return_Date,
                        'Itinerary' => json_encode($Itinerary),
                        'Recommendation' => json_encode($Recommendation),
                        'NumberOfRec' => count($Recommendation),
                        'Response_json' => json_encode($Response_Json),
                        'Response_XML' => $Response_XML,
                        'created_at' => Carbon::now()
                    ];
                }
            }
        }
        $this->info('start inserting : ' . count($insert) . ' items');
        foreach (array_chunk($insert, 10) as $searches) {
            AmadeusNewSearch::insert($searches);
        }
        $this->info('done');
    }
}
