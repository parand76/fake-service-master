<?php

namespace App\Console;

use App\Models\AmadeusNewFareRule;
use App\Models\AmadeusNewSearch;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AmadeusNewFareRulesResponses extends Command
{
    protected $signature = 'AmadeusNewFareRulesResponses';

    protected $description = '';

    public function handle()
    {
        $response = DB::table('logs')->where('message', 'LIKE', '%Supplier Response - AmadeusFareRulesFlight - Amadeus%')->get()->toArray();

        $insert = [];

        foreach ($response as $res) {
            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
            $body = $item['body'];

            $Response_XMl = preg_replace('#(.*?)<soap:Header>(.*?)</soap:Header>|</soap:Envelope>#','',$body);

            $namespace = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $body);
            $array = json_decode(json_encode(simplexml_load_string($namespace)), TRUE);

            $Response_Json = $array;

            if (isset($array['soap__Body']['MiniRule_GetFromRecReply'])) {
                if ($array['soap__Body']['MiniRule_GetFromRecReply']['responseDetails']['statusCode'] == 'O') {

                    $StatusCode = $array['soap__Body']['MiniRule_GetFromRecReply']['responseDetails']['statusCode'];
                    
                    if (isset($array['soap__Body']['MiniRule_GetFromRecReply']['mnrByPricingRecord'][0])) {
                        $ReferenceType = $array['soap__Body']['MiniRule_GetFromRecReply']['mnrByPricingRecord'][0]['pricingRecordId']['referenceType'];
                        $UniqueReference = $array['soap__Body']['MiniRule_GetFromRecReply']['mnrByPricingRecord'][0]['pricingRecordId']['uniqueReference'];
                    } else {
                        $ReferenceType = $array['soap__Body']['MiniRule_GetFromRecReply']['mnrByPricingRecord']['pricingRecordId']['referenceType'];
                        $UniqueReference = $array['soap__Body']['MiniRule_GetFromRecReply']['mnrByPricingRecord']['pricingRecordId']['uniqueReference'];
                    }

                    if (isset($array['soap__Body']['MiniRule_GetFromRecReply']['mnrByPricingRecord'][0])) {
                        $allCounts = [];
                        foreach($array['soap__Body']['MiniRule_GetFromRecReply']['mnrByPricingRecord'] as $count) {
                            $allCounts [] = count($count['paxRef']['passengerReference']);
                        }
                        $PassengersCount = implode(',',$allCounts);
                    } else {
                        $PassengersCount = count($array['soap__Body']['MiniRule_GetFromRecReply']['mnrByPricingRecord']['paxRef']['passengerReference']);;
                    }

                    if (isset($array['soap__Body']['MiniRule_GetFromRecReply']['mnrByPricingRecord']['fareComponentInfo'][0])) {
                        $OriginId = $array['soap__Body']['MiniRule_GetFromRecReply']['mnrByPricingRecord']['fareComponentInfo'][0]['originAndDestination']['origin'];
                        $DestinationOne = $array['soap__Body']['MiniRule_GetFromRecReply']['mnrByPricingRecord']['fareComponentInfo'][1]['originAndDestination']['destination'];
                        
                        if($OriginId == $DestinationOne) {
                            $Return = "true";
                            $DestinationId = $array['soap__Body']['MiniRule_GetFromRecReply']['mnrByPricingRecord']['fareComponentInfo'][0]['originAndDestination']['destination'];
                        }else {
                            $Return = "false";
                            $DestinationId = $array['soap__Body']['MiniRule_GetFromRecReply']['mnrByPricingRecord']['fareComponentInfo'][1]['originAndDestination']['destination'];
                        }
                    }

                    $insert[] = [
                        'RefrenceType' => $ReferenceType,
                        'UniqueRefrence' => $UniqueReference,
                        'StatusCode' => $StatusCode,
                        'Origin' => $OriginId,
                        'Destination' => $DestinationId,
                        'PassengersCount' => $PassengersCount,
                        'HasReturn' => $Return,
                        'Response_XML' => $Response_XMl,
                        'Response_Json' => json_encode($Response_Json),
                        'created_at' => Carbon::now(),
                    ];

                }
            }
        }
        $this->info('start inserting : ' . count($insert) . ' items');
        foreach (array_chunk($insert, 10) as $fare) {
            AmadeusNewFareRule::insert($fare);
        }
        $this->info('done');
    }
}
