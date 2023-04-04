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

$controller = 'Amadeus';

preg_match('/<(SearchFlight|Ping|GetFlightRules|BookFlight|CreateTicket|SignOut)/m', request()->getContent(), $output_array);
if (empty($output_array[1])) {
    $action = null;
    $name = null;
    return;
}
switch ($output_array[1]) {
    case 'SearchFlight':
        $action = 'search';
        $name = 'amadeusSearch';
        break;
    case 'Ping':
        $action = 'ping';
        $name = 'amadeusPing';
        break;
    case 'GetFlightRules':
        $action = 'fareRules';
        $name = 'amadeusFareRules';
        break;
    case 'BookFlight':
        // lugInfo('route',[$output_array]);

        $action = 'booking';
        $name = 'amadeusBook';
        break;
    case 'CreateTicket':
        $action = 'createTicket';
        $name = 'amadeusCreateTicket';
        break;
    case 'SignOut':
        $action = 'singOut';
        $name = 'amadeusSingOut';
        break;
}
