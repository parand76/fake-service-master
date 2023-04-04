<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->post('api/Partners/Flight/V2/Availability', 'NewItours@Availability');

$router->post('api/Partners/Flight/V2/Authenticate','NewItours@Authenticate');

$router->get('api/Partners/Flight/V2/GetAvailability','NewItours@getAvailibility');
$router->get('api/Partners/Flight/V2/GetFlightRules','NewItours@getFlightRules');
$router->post('api/Partners/Flight/V2/Validate','NewItours@validateFlight');
$router->post('api/Partners/Flight/V2/Reserve','NewItours@reserve');
$router->get('api/Partners/Flight/V2/GetReserveDetail','NewItours@getReserveDetail');

$router->post('api/Partners/Flight/V2/Confirm','NewItours@confirmation');
