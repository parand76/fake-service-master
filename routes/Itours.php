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
$router->post('api/services/app/BookingFlight/LowFareSearch', 'Itours@LowFareSearch');
$router->get('api/services/app/BookingFlight/GetFlightRules', 'Itours@getFlightRules');
$router->post('api/services/app/BookingFlight/Validate', 'Itours@validateFlight');
$router->post('api/services/app/BookingFlight/ReservePNR', 'Itours@reservePNR');
$router->get('api/services/app/BookingFlight/GetPNRDetails', 'Itours@getPnrDetails');
$router->get('api/services/app/BookingFlight/GetFlightReserveById','Itours@GetFlightReserveById');
$router->post('api/v1/Affiliate/Reserves/Deposit/ConfirmByDeposit','Itours@confirmByDEposit');
$router->get('api/services/app/BookingFlight/GetDirectTicketById','Itours@getDirectTicketById');
$router->post('api/TokenAuth/Authenticate','Itours@Authenticate');

$router->post('api/services/app/BookingFlight/IssuePNR','Itours@issuePnr');
$router->post('api/services/app/BookingFlight/PricePnr','Itours@pricePnr');

