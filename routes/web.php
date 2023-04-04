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


$router->get('/', function () use ($router) {
    return $router->app->version();
});


//amadeus header stuff
$router->get('sup/api/ama', function () {

    $password = "aBII^1B2#4#W";

    $nonce = random_bytes(32); # requires PHP 7

    date_default_timezone_set("UTC");

    $timestamp = date(DATE_ATOM);

    $encodedNonce = base64_encode($nonce);

    $passSHA = base64_encode(sha1($nonce . $timestamp . sha1($password, true), true));

    return  [

        'encodedNonce' => $encodedNonce,

        'timestamp' => $timestamp,

        'passSHA' => $passSHA,

    ];
});
