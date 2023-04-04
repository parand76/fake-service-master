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
$controller = 'Accelaero';
preg_match('/(<ns:OTA_AirAvailRQ|<ns2:OTA_AirPriceRQ|<ns2:OTA_AirBookRQ|<ns:AA_OTA_AirBaggageDetailsRQ)/m', request()->getContent(), $output_array);

if (empty($output_array[1])) {
    $action = null;
    $name = null;
    return;
}

switch ($output_array[1]) {
    case '<ns:OTA_AirAvailRQ':
        $action = 'RetriveFlightAvailability';
        break;
    case '<ns2:OTA_AirPriceRQ':
        $action = 'GetPriceQuote';
        break;
    case '<ns2:OTA_AirBookRQ':
        $action = 'Booking';
        break;
    case '<ns:AA_OTA_AirBaggageDetailsRQ':
        $action = 'BaggageDetail';
        break;
}