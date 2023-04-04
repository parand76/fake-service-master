<?php

namespace App\Http\Middleware;

use Closure;

class FlyErbilXml
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
        request()->merge($array);
        lugInfo('end middlware',[]);
        return $next($request);
    }
}
