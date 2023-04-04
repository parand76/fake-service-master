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

$router->post('authenticate', 'CityNetController@Login');
$router->post('flights/search', 'CityNetController@Search');
$router->post('flights/rules', 'CityNetController@Rules');
$router->post('flights/book', 'CityNetController@Book');
$router->get('flights/ticket/ContractNo/{contractNo}/Credit/true/Wallet/false/Currency/IRR', 'CityNetController@Ticket');
$router->get('report/contract/passenger/byitinerary', 'CityNetController@SingleReport');