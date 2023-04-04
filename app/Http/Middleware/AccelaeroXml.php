<?php

namespace App\Http\Middleware;

use Closure;

class AccelaeroXml
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
            $xml = preg_replace('/(>\s+<)/', '><', $xml);
            lugWarning('middlware xml',[$xml]);
            $array = json_decode(json_encode(simplexml_load_string($xml)), TRUE);
        } catch (\Throwable $th) {
            return response(1, 400);
        }
        if (empty($array['soap__Header'])) {
            return $next($request);
        } else {
            request()->merge($array['soap__Header']);
            request()->merge($array['soap__Body']);
        }
        return $next($request);
    }
}
