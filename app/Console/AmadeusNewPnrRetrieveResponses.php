<?php

namespace App\Console;

use App\Models\AmadeusNewPnrRetrieve;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AmadeusNewPnrRetrieveResponses extends Command
{
    protected $signature = 'AmadeusNewPnrRetrieveResponses';

    protected $description = '';

    public function handle()
    {
        $response = DB::table('logs')->where('message', 'LIKE', '%Supplier Response - AmadeusAsyncTicketingFlight - Amadeus%')->get();

        $insert = [];
        foreach ($response as $res) {

            if (isset(json_decode($res->info, true)['serialize']['curlResponse'])) {
                $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);

                $body = $item['body'];

                $Response_XML = preg_replace('#(.*?)<soap:Header>(.*?)</soap:Header>|</soap:Envelope>#','',$body);

                $namespace = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $body);
                $array = json_decode(json_encode(simplexml_load_string($namespace)), TRUE);

                $Response_Json = $array;

                // $insert [] = $array;


                if(isset($array['soap__Body']['PNR_Reply'])) {

                    ///> company id , control number
                    $CompanyId = $array['soap__Body']['PNR_Reply']['pnrHeader']['reservationInfo']['reservation']['companyId'];
                    $ControlNumber = $array['soap__Body']['PNR_Reply']['pnrHeader']['reservationInfo']['reservation']['controlNumber'];
                
                    ///> origin and destination location
                    if(isset($array['soap__Body']['PNR_Reply']['originDestinationDetails']['itineraryInfo'][0])) {
                        ///> rounded
                        $Origin = $array['soap__Body']['PNR_Reply']['originDestinationDetails']['itineraryInfo'][0]['legInfo']['legTravelProduct']['boardPointDetails']['trueLocationId'];
                        $Destination = $array['soap__Body']['PNR_Reply']['originDestinationDetails']['itineraryInfo'][0]['legInfo']['legTravelProduct']['offpointDetails']['trueLocationId'];
                        $DepratureDate = $array['soap__Body']['PNR_Reply']['originDestinationDetails']['itineraryInfo'][0]['legInfo']['legTravelProduct']['flightDate']['departureDate'];
                        $ReturnDate = $array['soap__Body']['PNR_Reply']['originDestinationDetails']['itineraryInfo'][1]['legInfo']['legTravelProduct']['flightDate']['departureDate'];
                    } else {
                        ///> one way
                    }

                    ///> passengers count
                    $ADT = 0;
                    $CHD = 0;
                    $INF = 0;
                    if(isset($array['soap__Body']['PNR_Reply']['travellerInfo'][0])) {

                        foreach($array['soap__Body']['PNR_Reply']['travellerInfo'] as $travel) {

                            if(isset($travel['passengerData'][0])) {

                                foreach($travel['passengerData'] as $passenger) {
                                    
                                    if($passenger['travellerInformation']['passenger']['type'] == 'ADT') {
                                        $ADT++;
                                    }
                                    if($passenger['travellerInformation']['passenger']['type'] == 'CHD') {
                                        $CHD++;
                                    }
                                    if($passenger['travellerInformation']['passenger']['type'] == 'INF') {
                                        $INF++;
                                    }
                                }

                            } else {
                                
                                if($travel['passengerData']['travellerInformation']['passenger']['type'] == 'ADT') {
                                    $ADT++;
                                }
                                if($travel['passengerData']['travellerInformation']['passenger']['type'] == 'CHD') {
                                    $CHD++;
                                }
                                if($travel['passengerData']['travellerInformation']['passenger']['type'] == 'INF') {
                                    $INF++;
                                }

                            }

                        }

                    } else {
                       
                        if(isset($array['soap__Body']['PNR_Reply']['travellerInfo']['passengerData'][0])) {

                            foreach($array['soap__Body']['PNR_Reply']['travellerInfo']['passengerData'] as $passData) {

                                if($passData['travellerInformation']['passenger']['type'] == 'ADT') {
                                    $ADT++;
                                }
                                if($passData['travellerInformation']['passenger']['type'] == 'CHD') {
                                    $CHD++;
                                }
                                if($passData['travellerInformation']['passenger']['type'] == 'INF') {
                                    $INF++;
                                }

                            }

                        } else {

                            if($array['soap__Body']['PNR_Reply']['travellerInfo']['passengerData']['travellerInformation']['passenger']['type'] == 'ADT') {
                                $ADT++;
                            }
                            if($array['soap__Body']['PNR_Reply']['travellerInfo']['passengerData']['travellerInformation']['passenger']['type'] == 'CHD') {
                                $CHD++;
                            }
                            if($array['soap__Body']['PNR_Reply']['travellerInfo']['passengerData']['travellerInformation']['passenger']['type'] == 'INF') {
                                $INF++;
                            }

                        }
                    }
                    
                    $insert[] = [
                        'CompanyId' => $CompanyId,
                        'ControlNumber' => $ControlNumber,
                        'ADT' => $ADT,
                        'CHD' => $CHD,
                        'INF' => $INF,
                        'Origin' => $Origin,
                        'Destination' => $Destination,
                        'DepratureDate' => $DepratureDate,
                        'ReturnDate' => $ReturnDate,
                        'Response_XML' => $Response_XML,
                        'Response_json' => json_encode($Response_Json),
                        'created_at' => Carbon::now(),
                    ];
                }

            }
        }

        $this->info('start inserting : ' . count($insert) . ' items');
        
        foreach (array_chunk($insert, 10) as $searches) {
            AmadeusNewPnrRetrieve::insert($searches);
        }

        $this->info('done');
    }
}
