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
$controller = 'TBOController';
preg_match('/<hot:(HotelSearchRequest|HotelRoomAvailabilityRequest|HotelDetailsRequest|AvailabilityAndPricingRequest|HotelBookRequest|GenerateInvoiceRequest|CountryListRequest)/m', request()->getContent(), $output_array);
if (empty($output_array[1])) {
    $action = null;
    $name = null;
    return;
}
switch ($output_array[1]) {
    case 'HotelSearchRequest':
        $action = 'searchHotel';
        break;
    case 'HotelRoomAvailabilityRequest':
        $action = 'avilableHotels';
        break;
    case 'HotelDetailsRequest':
        $action = 'hotelDetails';
        break;
    case 'AvailabilityAndPricingRequest':
        $action = 'availibilityAndPricing';
        break;
    case 'HotelBookRequest':
        $action = 'bookHotel';
        break;
    case 'GenerateInvoiceRequest':
        $action = 'generateInvoice';
        break;
    case 'CountryListRequest':
        $action = 'countryList';
        break;
}
