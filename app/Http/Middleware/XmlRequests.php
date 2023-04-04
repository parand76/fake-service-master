<?php

namespace App\Http\Middleware;

use Closure;

class XmlRequests
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $xml = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $request->getContent());
            
            $array = json_decode(json_encode(simplexml_load_string($xml)), TRUE);
            

        } catch (\Throwable $th) {
            lugError($th->getMessage());
            return response(1, 400);
        }
        // dd($request->routeIs('amadeusSearch'));
        if (isset($array['soap__Body']['SearchFlight'])) {
            lugInfo('middlware SearchFlight',[]);
            $request->merge($array['soap__Header']['AuthenticationSoapHeader']);
            $request->merge($array['soap__Body']['SearchFlight']['OTA_AirLowFareSearchRQ']);
        }
        if (empty($array['soap__Header'])) {
            lugInfo('middlware empty',[]);
            return $next($request);
        } else {
            lugInfo('middlware else',[]);
            $request->merge($array['soap__Header']['AuthenticationSoapHeader']);
            $request->merge($array['soap__Body']);
        }
        lugInfo('end middlware',[]);
        return $next($request);
    }
}
