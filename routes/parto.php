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

///> Air Routes
$router->post('Rest/Authenticate/CreateSession', 'Session@create');
$router->post('Rest/Authenticate/EndSession', 'Session@end');
$router->post('Rest/Air/AirLowFareSearch', 'Air@lowFareSearch');
$router->post('Rest/Air/AirBook', 'Air@book');
$router->post('Rest/Air/AirOrderTicket', 'Air@orderTicket');
$router->post('Rest/Air/AirBookingData', 'Air@bookingData');
$router->post('Rest/Air/AirRules', 'Air@fareRule');

///> Hotel Routes
$router->post('Rest/Hotel/HotelAvailability','Hotel@Availability');
$router->post('Rest/Hotel/HotelCheckRate','Hotel@CheckRate');
$router->post('Rest/Hotel/HotelBook','Hotel@Book');
$router->post('Rest/Hotel/HotelOrder','Hotel@Order');
$router->post('Rest/Hotel/HotelBookingData','Hotel@BookingData');
$router->post('Rest/Hotel/HotelImage','Hotel@Image');
$router->post('Rest/Hotel/DomesticHotelImage','Hotel@DomesticHotelImage');
