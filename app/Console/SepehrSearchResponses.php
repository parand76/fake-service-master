<?php

namespace App\Console;

use App\Models\SepehrFlightSearch;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SepehrSearchResponses extends Command
{
    protected $signature = 'SepehrSearchResponses';

    protected $description = '';

    public function handle()
    {
        $response = DB::table('tmp')->where('message', 'LIKE', '%Supplier Response - SepehrSearchFlight%')->get();
        $insert = [];
        foreach ($response as $res) {

            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
            $body = $item[0]['body'];
            $array = json_decode($body, true);

            if (!empty($array)) {

                $CharterFlight = !empty($array['CharterFlights']) ? $array['CharterFlights'] : null;
                $WebserviceFlight = !empty($array['WebserviceFlights']) ? $array['WebserviceFlights'] : null;
                $OriginCode = !empty($array['CharterFlights']) ? $array['CharterFlights'][0]['Origin']['Code'] : (!empty($array['WebserviceFlights']) ? $array['WebserviceFlights'][0]['Origin']['Code'] : null);
                $DestinationCode = !empty($array['CharterFlights']) ? $array['CharterFlights'][0]['Destination']['Code'] : (!empty($array['WebserviceFlights']) ? $array['WebserviceFlights'][0]['Destination']['Code'] : null);
                $DepartureDate = !empty($array['CharterFlights']) ? $array['CharterFlights'][0]['DepartureDateTime'] : (!empty($array['WebserviceFlights']) ? $array['WebserviceFlights'][0]['DepartureDateTime'] : null);
                $CurrencyCode = $array['CurrencyCode'];

                $insert[] = [
                    "OriginCode" => $OriginCode,
                    "DestinationCode" => $DestinationCode,
                    "DepartureDateTime" => $DepartureDate,
                    "CurrencyCode" => $CurrencyCode,
                    "CharterFlights" => json_encode($CharterFlight),
                    "WebserviceFlights" => json_encode($WebserviceFlight),
                    "responses" => json_encode($array),
                    "created_at" => Carbon::now(),
                ];
            }
        }

        $this->info('start inserting : ' . count($insert) . ' items');
        
        foreach (array_chunk($insert, 10) as $flight) {

            SepehrFlightSearch::insert($flight);
            $this->info('done');
        }
    }
}
