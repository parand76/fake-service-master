<?php

use Carbon\Carbon;

if (!function_exists('convertXmlToArrayAmadeus')) {
    function convertXmlToArrayAmadeus($body)
    {
        try {
            $namespace = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $body);
            $array = json_decode(json_encode(simplexml_load_string($namespace)), TRUE);
        } catch (\Throwable $th) {
            return response(1, 400);
        }
        if (isset($array['soap__Body']['SearchFlight'])) {
            request()->merge($array['soap__Header']['AuthenticationSoapHeader']);
            request()->merge($array['soap__Body']['SearchFlight']['OTA_AirLowFareSearchRQ']);
        } else {
            request()->merge($array['soap__Header']['AuthenticationSoapHeader']);
            request()->merge($array['soap__Body']);
        }
    }
}

if (!function_exists('convertXmlToArrayTBo')) {
    function convertXmlToArrayTBo($body)
    {
        $namespace = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $body);
        $array = json_decode(json_encode(simplexml_load_string($namespace)), TRUE);
        request()->merge($array['soap__Header']);
        request()->merge($array['soap__Body']);
    }
}
if (!function_exists('bladeRenderTbo')) {
    function bladeRenderTbo($body)
    {
        $namespace = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $body);
        $array = json_decode(json_encode(simplexml_load_string($namespace)), TRUE);
        request()->merge($array['s__Header']);
        request()->merge($array['s__Body']);
        return request();
    }
}
if (!function_exists('randomDateTime')) {
    function randomDateTime($start, $sFormat = 'Y-m-d H:i:s')
    {
        // Convert the supplied date to timestamp
        $fMin = strtotime($start);
        $fMax = strtotime(Carbon::create($start)->addHours(24));
        $fVal = mt_rand($fMin, $fMax);

        // Convert back to the specified date format

        return date($sFormat, $fVal);
    }
}

if (!function_exists('getDiffDate')) {

    function getDiffDate($start, $end)
    {
        $x = $end->diff($start);
        // dd($start, $end);
        $time = $start->format('Y-m-d') . 'T' . $x->format('%H:%I:%S');
        // dd($x);
        return $time;
    }
}
if (!function_exists('repel')) {
    function repel(&$array){
        if(empty($array)){
            return;
        }
        if(empty($array[0])){
            $array=[$array];
        }
    } 
}
