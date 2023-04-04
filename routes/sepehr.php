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

$router->post('api/Partners/Flight/Availability/V8/SearchByRouteAndDate', 'SepehrFlight@Search');
$router->post('api/Partners/Flight/Booking/V9/Book', 'SepehrFlight@Booking');
$router->post('api/OpenTravelAlliance/Generic/CurrentBalanceV6', 'SepehrFlight@CurrentBalance');
