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
$controller = '';
preg_match('/<(OTA_AirLowFareSearchRQ|OTA_AirPriceRQ|OTA_AirBookRQ|OTA_AirDemandTicketRQ)/m', request()->getContent(), $output_array);
if (empty($output_array[1])) {
    $action = null;
    $name = null;
    return;
}
switch ($output_array[1]) {
    case 'OTA_AirLowFareSearchRQ':
        $controller = 'FlyErbil';
        $action = 'airLowFareSearch';
        break;
    case 'OTA_AirPriceRQ':
        $controller = 'FlyErbil';
        $action = 'getPrice';
        break;
    case 'OTA_AirBookRQ':
        $controller = 'BookAndPeyment';
        $action = 'airBook';
        break;
    case 'OTA_AirDemandTicketRQ':
        $controller = 'BookAndPeyment';
        $action = 'peyment';
        break;
}
// $router->post('test-skywork-gds/ota-ecom-saml', 'FlyErbil@airLowFareSearch');
