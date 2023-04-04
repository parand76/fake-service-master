<?php

namespace App\Http\Controllers\Amadeus;

use App\Http\Controllers\Controller;
use App\Models\AmadeusBook;
use App\Models\AmadeusCreateTicket;
use App\Models\AmadeusSearch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie as FacadesCookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Cookie;

class Amadeus extends Controller
{
    protected $search = [];

    public function search()
    {
        extract(request()->all());

        $header = request()->header();
        if (empty($header['soapaction']) || $header['soapaction'][0] != "http://epowerv5.amadeus.com.tr/WS/SearchFlight") {
            lugError('there is error in header', [$header]);
            return response(view("Amadeus.soapActionError"), 200);
        }
        $a = '<Error Type="%s" ShortText="%s" Code="%s" Tag="%s" NodeList="%s" BreakFlow="false" />';
        $errors = [];
        if (empty($WSUserName)) {
            $error = "User Name is required";
            return view('Amadeus.validateAuth', compact('error'));
        }

        if ($WSUserName != 'fakeAmadeus') {
            $error = "We were unable to access your information";
            return view('Amadeus.validateAuth', compact('error'));
        }

        if ($WSPassword != 'matinserver') {
            $error = "We were unable to access your information";
            return view('Amadeus.validateAuth', compact('error'));
        }
        if (empty($WSPassword)) {
            $error = "Password is required";
            return view('Amadeus.validateAuth', compact('error'));
        }

        if (isset($OriginDestinationInformation[0])) {
            if (
                empty($OriginDestinationInformation[0]['DepartureDateTime']) ||
                empty($OriginDestinationInformation[1]['DepartureDateTime']) ||
                empty($TravelPreferences['CabinPref']['@attributes']['Cabin'])
            ) {
                lugError('error in origin destination',[$OriginDestinationInformation]);
                return view('Amadeus.validateBody');
            }
            if ($OriginDestinationInformation[0]['DepartureDateTime'] < Carbon::now() && $OriginDestinationInformation[1]['DepartureDateTime'] < Carbon::now()) {
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'DepartureDate', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'DepartureDate', 'FlightFareDrivenSearchItinerary');
            }
            if ($OriginDestinationInformation[0]['DepartureDateTime'] < Carbon::now()) {
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'DepartureDate', 'FlightFareDrivenSearchItinerary');
            }

            if ($OriginDestinationInformation[1]['DepartureDateTime'] < Carbon::now()) {
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'ReturnDate', 'FlightFareDrivenSearchItinerary');
            }

            if ($OriginDestinationInformation[1]['DepartureDateTime'] < $OriginDestinationInformation[0]['DepartureDateTime']) {
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotGreaterThanForDate', 'A020', 'DepartureDate', 'FlightFareDrivenSearchItineraries');
            }

            if ($OriginDestinationInformation[1]['DepartureDateTime'] < $OriginDestinationInformation[0]['DepartureDateTime'] && $OriginDestinationInformation[1]['DepartureDateTime'] < Carbon::now()) {
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotGreaterThanForDate', 'A020', 'DepartureDate', 'FlightFareDrivenSearchItineraries');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'ReturnDate', 'FlightFareDrivenSearchItinerary');
            }

            if (
                $OriginDestinationInformation[1]['DepartureDateTime'] < $OriginDestinationInformation[0]['DepartureDateTime'] &&
                $OriginDestinationInformation[1]['DepartureDateTime'] < Carbon::now() &&
                (empty($OriginDestinationInformation[0]['OriginLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['OriginLocation']['@attributes']['LocationCode']))
            ) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotGreaterThanForDate', 'A020', 'DepartureDate', 'FlightFareDrivenSearchItineraries');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'From', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'ReturnDate', 'FlightFareDrivenSearchItinerary');
            }

            if (
                $OriginDestinationInformation[1]['DepartureDateTime'] < $OriginDestinationInformation[0]['DepartureDateTime'] &&
                $OriginDestinationInformation[1]['DepartureDateTime'] < Carbon::now() &&
                (empty($OriginDestinationInformation[0]['OriginLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['OriginLocation']['@attributes']['LocationCode'])) &&
                (empty($OriginDestinationInformation[0]['DestinationLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['DestinationLocation']['@attributes']['LocationCode']))
            ) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotGreaterThanForDate', 'A020', 'DepartureDate', 'FlightFareDrivenSearchItineraries');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'From', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'To', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'ReturnDate', 'FlightFareDrivenSearchItinerary');
            }

            if (
                $OriginDestinationInformation[1]['DepartureDateTime'] < $OriginDestinationInformation[0]['DepartureDateTime'] &&
                $OriginDestinationInformation[1]['DepartureDateTime'] < Carbon::now() &&
                $OriginDestinationInformation[1]['DepartureDateTime'] < Carbon::now() &&
                (empty($OriginDestinationInformation[0]['OriginLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['OriginLocation']['@attributes']['LocationCode'])) &&
                (empty($OriginDestinationInformation[0]['DestinationLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['DestinationLocation']['@attributes']['LocationCode']))
            ) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotGreaterThanForDate', 'A020', 'DepartureDate', 'FlightFareDrivenSearchItineraries');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'DepartureDate', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'From', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'To', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'ReturnDate', 'FlightFareDrivenSearchItinerary');
            }

            if (
                $OriginDestinationInformation[1]['DepartureDateTime'] < $OriginDestinationInformation[0]['DepartureDateTime'] &&
                $OriginDestinationInformation[1]['DepartureDateTime'] < Carbon::now() &&
                $OriginDestinationInformation[1]['DepartureDateTime'] < Carbon::now() &&
                (empty($OriginDestinationInformation[0]['OriginLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['OriginLocation']['@attributes']['LocationCode']))
            ) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotGreaterThanForDate', 'A020', 'DepartureDate', 'FlightFareDrivenSearchItineraries');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'DepartureDate', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'From', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'ReturnDate', 'FlightFareDrivenSearchItinerary');
            }

            if (
                $OriginDestinationInformation[1]['DepartureDateTime'] < $OriginDestinationInformation[0]['DepartureDateTime'] &&
                $OriginDestinationInformation[1]['DepartureDateTime'] < Carbon::now() &&
                $OriginDestinationInformation[1]['DepartureDateTime'] < Carbon::now() &&
                (empty($OriginDestinationInformation[0]['DestinationLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['DestinationLocation']['@attributes']['LocationCode']))
            ) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotGreaterThanForDate', 'A020', 'DepartureDate', 'FlightFareDrivenSearchItineraries');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'DepartureDate', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'To', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'ReturnDate', 'FlightFareDrivenSearchItinerary');
            }

            if (
                $OriginDestinationInformation[1]['DepartureDateTime'] < $OriginDestinationInformation[0]['DepartureDateTime'] &&
                (empty($OriginDestinationInformation[0]['DestinationLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['DestinationLocation']['@attributes']['LocationCode']))
            ) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotGreaterThanForDate', 'A020', 'DepartureDate', 'FlightFareDrivenSearchItineraries');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'To', 'FlightFareDrivenSearchItinerary');
            }

            if (
                $OriginDestinationInformation[1]['DepartureDateTime'] < $OriginDestinationInformation[0]['DepartureDateTime'] &&
                (empty($OriginDestinationInformation[0]['OriginLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['OriginLocation']['@attributes']['LocationCode']))
            ) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotGreaterThanForDate', 'A020', 'DepartureDate', 'FlightFareDrivenSearchItineraries');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'From', 'FlightFareDrivenSearchItinerary');
            }

            if (
                $OriginDestinationInformation[0]['DepartureDateTime'] < Carbon::now()  &&
                (empty($OriginDestinationInformation[0]['OriginLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['OriginLocation']['@attributes']['LocationCode']))
            ) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'DepartureDate', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'From', 'FlightFareDrivenSearchItinerary');
            }

            if (
                $OriginDestinationInformation[0]['DepartureDateTime'] < Carbon::now()  &&
                (empty($OriginDestinationInformation[0]['DestinationLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['DestinationLocation']['@attributes']['LocationCode']))
            ) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'DepartureDate', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'To', 'FlightFareDrivenSearchItinerary');
            }

            if (
                $OriginDestinationInformation[0]['DepartureDateTime'] < Carbon::now()  &&
                (empty($OriginDestinationInformation[0]['OriginLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['OriginLocation']['@attributes']['LocationCode'])) &&
                (empty($OriginDestinationInformation[0]['DestinationLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['DestinationLocation']['@attributes']['LocationCode']))
            ) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'DepartureDate', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'From', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'To', 'FlightFareDrivenSearchItinerary');
            }
            if ($OriginDestinationInformation[1]['DepartureDateTime'] < $OriginDestinationInformation[0]['DepartureDateTime'] && $OriginDestinationInformation[0]['DepartureDateTime'] < Carbon::now() && $OriginDestinationInformation[1]['DepartureDateTime'] < Carbon::now()) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotGreaterThanForDate', 'A020', 'DepartureDate', 'FlightFareDrivenSearchItineraries');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'DepartureDate', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'ReturnDate', 'FlightFareDrivenSearchItinerary');
            }

            if (empty($OriginDestinationInformation[0]['OriginLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['OriginLocation']['@attributes']['LocationCode'] || $OriginDestinationInformation[0]['OriginLocation']['@attributes']['LocationCode'] && $OriginDestinationInformation[1]['OriginLocation']['@attributes']['LocationCode'])) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'From', 'FlightFareDrivenSearchItinerary');
            }
            if ((empty($OriginDestinationInformation[0]['OriginLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['OriginLocation']['@attributes']['LocationCode'])) &&
                (empty($OriginDestinationInformation[0]['DestinationLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['DestinationLocation']['@attributes']['LocationCode']))
            ) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'From', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'To', 'FlightFareDrivenSearchItinerary');
            }
            if (empty($OriginDestinationInformation[0]['DestinationLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[1]['DestinationLocation']['@attributes']['LocationCode']) || empty($OriginDestinationInformation[0]['DestinationLocation']['@attributes']['LocationCode']) && empty($OriginDestinationInformation[1]['DestinationLocation']['@attributes']['LocationCode'])) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'To', 'FlightFareDrivenSearchItinerary');
            }
        } else {
            if ($OriginDestinationInformation['DepartureDateTime'] < Carbon::now()) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'DepartureDate', 'FlightFareDrivenSearchItinerary');
            }
            if (empty($OriginDestinationInformation['OriginLocation']['@attributes']['LocationCode'])) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'From', 'FlightFareDrivenSearchItinerary');
            }

            if (empty($OriginDestinationInformation['OriginLocation']['@attributes']['LocationCode']) && $OriginDestinationInformation['DepartureDateTime'] < Carbon::now()) {

                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'DepartureDate', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'From', 'FlightFareDrivenSearchItinerary');
            }

            if (empty($OriginDestinationInformation['OriginLocation']['@attributes']['LocationCode']) && $OriginDestinationInformation['DepartureDateTime'] < Carbon::now() && empty($OriginDestinationInformation['DestinationLocation']['@attributes']['LocationCode'])) {
                $errors[] = sprintf($a, 'ValidationError', 'Validation_WrongCode', 'A017', 'City', 'FlightFareDrivenSearchInfo');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A012', 'DepartureDate', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'From', 'FlightFareDrivenSearchItinerary');
                $errors[] = sprintf($a, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'To', 'FlightFareDrivenSearchItinerary');
            }
        }
        if (isset($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity'][0])) {
            if (count($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity']) == 3) {
                if (empty($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity'][0]['@attributes']['Quantity']) || empty($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity'][1]['@attributes']['Quantity']) || empty($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity'][2]['@attributes']['Quantity'])) {
                    return response(view('Amadeus.erorPassengerQuantity'), 200);
                }
                if (empty($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity'][0]['@attributes']['Code']) || empty($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity'][1]['@attributes']['Code']) || empty($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity'][2]['@attributes']['Code'])) {
                    return response(view('Amadeus.errorPassenger'), 200);
                }
            }

            if (count($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity']) == 2) {
                if (empty($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity'][0]['@attributes']['Quantity']) || empty($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity'][1]['@attributes']['Quantity'])) {
                    return response(view('Amadeus.erorPassengerQuantity'), 200);
                }
                if (empty($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity'][0]['@attributes']['Code']) || empty($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity'][1]['@attributes']['Code'])) {
                    return response(view('Amadeus.errorPassenger'), 200);
                }
            }
        }
        if (isset($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity']['@attributes'])) {

            if (empty($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity']['@attributes']['Quantity'])) {
                return response(view('Amadeus.erorPassengerQuantity'), 200);
            }
            if (empty($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity']['@attributes']['Code'])) {
                return response(view('Amadeus.errorPassenger'), 200);
            }
        }

        if (!empty($errors)) {
            return response(view('Amadeus.timeError', ['errorlist' => $errors]), 200);
        }


        $curency = 'USD';
        if (isset($AdvanceSearchInfo['Currency'])) {
            $curency = $AdvanceSearchInfo['Currency'];
        }
        if (empty($OriginDestinationInformation['DepartureDateTime'])) {
            $originCode = $OriginDestinationInformation[0]['OriginLocation']['@attributes']['LocationCode'];
            $destinationCode = $OriginDestinationInformation[0]['DestinationLocation']['@attributes']['LocationCode'];
            $destinationCodeBack = $OriginDestinationInformation[1]['DestinationLocation']['@attributes']['LocationCode'];
            $originCodeBack = $OriginDestinationInformation[1]['OriginLocation']['@attributes']['LocationCode'];

            if ($OriginDestinationInformation[1]['OriginLocation']['@attributes']['MultiAirportCityInd'] == "true") {
                $cityId = DB::table('city_supplier')->where('data_reference', 1201)->where('supplier_key', $OriginDestinationInformation[0]['OriginLocation']['@attributes']['LocationCode'])->pluck('city_id')->first();
                $originCode = DB::table('airports')->where('city_id', $cityId)->pluck('abb')->first();
            }

            if ($OriginDestinationInformation[0]['DestinationLocation']['@attributes']['MultiAirportCityInd'] == "true") {
                $cityId = DB::table('city_supplier')->where('data_reference', 1201)->where('supplier_key', $OriginDestinationInformation[0]['DestinationLocation']['@attributes']['LocationCode'])->pluck('city_id')->first();
                $destinationCode = DB::table('airports')->where('city_id', $cityId)->pluck('abb')->first();
            }

            if ($OriginDestinationInformation[1]['OriginLocation']['@attributes']['MultiAirportCityInd'] == "true") {
                $cityId = DB::table('city_supplier')->where('data_reference', 1201)->where('supplier_key', $OriginDestinationInformation[0]['OriginLocation']['@attributes']['LocationCode'])->pluck('city_id')->first();
                $originCodeBack = DB::table('airports')->where('city_id', $cityId)->pluck('abb')->first();
            }

            if ($OriginDestinationInformation[1]['DestinationLocation']['@attributes']['MultiAirportCityInd'] == "true") {
                $cityId = DB::table('city_supplier')->where('data_reference', 1201)->where('supplier_key', $OriginDestinationInformation[0]['DestinationLocation']['@attributes']['LocationCode'])->pluck('city_id')->first();
                $destinationCodeBack = DB::table('airports')->where('city_id', $cityId)->pluck('abb')->first();
            }

            $this->search = [
                'departure' => $OriginDestinationInformation[0]['DepartureDateTime'],
                'departurLocationCode' => $originCode,
                'arivelLocationCode' => $destinationCode,
                'departureBack' => $OriginDestinationInformation[1]['DepartureDateTime'],
                'destinationCodeBack' => $destinationCodeBack,
                'originCodeBack' => $originCodeBack,
                'curency' => $curency
            ];
        } else {
            $originCode = $OriginDestinationInformation['OriginLocation']['@attributes']['LocationCode'];
            $destinationCode = $OriginDestinationInformation['DestinationLocation']['@attributes']['LocationCode'];
            if ($OriginDestinationInformation['OriginLocation']['@attributes']['MultiAirportCityInd'] == "true") {
                $cityId = DB::table('city_supplier')->where('data_reference', 1201)->where('supplier_key', $OriginDestinationInformation['OriginLocation']['@attributes']['LocationCode'])->pluck('city_id')->first();
                $originCode = DB::table('airports')->where('city_id', $cityId)->pluck('abb')->first();
            }
            if ($OriginDestinationInformation['DestinationLocation']['@attributes']['MultiAirportCityInd'] == "true") {
                $cityId = DB::table('city_supplier')->where('data_reference', 1201)->where('supplier_key', $OriginDestinationInformation['DestinationLocation']['@attributes']['LocationCode'])->pluck('city_id')->first();
                $destinationCode = DB::table('airports')->where('city_id', $cityId)->pluck('abb')->first();
            }
            $this->search = [
                'departure' => $OriginDestinationInformation['DepartureDateTime'],
                'departurLocationCode' => $originCode,
                'arivelLocationCode' => $destinationCode,
                'curency' => $curency
            ];
        }
        if (isset($WSPassword) && isset($WSUserName) && empty($errors)) {
            if (isset($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity'][0])) {
                foreach ($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity'] as  $passengr) {
                    switch ($passengr['@attributes']['Code']) {
                        case 'ADT':
                            $adultCount = $passengr['@attributes']['Quantity'];
                            break;
                        case 'INF':
                            $infantCount = $passengr['@attributes']['Quantity'];
                            break;
                        case 'CHD':
                            $chiledCount = $passengr['@attributes']['Quantity'];
                            break;
                    }
                }

                $this->search['adult'] = $adultCount;
                $this->search['child'] = $chiledCount  ?? null;
                $this->search['infant'] = $infantCount ?? null;
            }
            if (isset($TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity']['@attributes'])) {
                $adultCount = $TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity']['@attributes']['Quantity'];
                $this->search['adult'] =  $adultCount;
            }
            $list = [];
            $random = rand(0, 20);
            for ($i = 0; $i <= $random; $i++) {
                $list[$i] = $this->generatePriceItinerary();
            }

            if (isset($OriginDestinationInformation[0])) {
                $onewayCombinable = $this->generatePriceItinerary(true);
            }
            if (isset($header['cookie'][0])) {
                preg_match('/(?<=ASP.NET_SessionId=)[^;]+/i', $header['cookie'][0], $id);
                $session = AmadeusSearch::where('sessionId', $id[0])->where('expired_at', '>=', Carbon::now())->first();
            }
            if (isset($session)) {
                $session->expired_at = Carbon::yesterday();
                $session->save();
                $name = 'ASP.NET_SessionId';
                $domain = "staging-ws.epower.amadeus.com";
                $sessionId = "ASP.NET_SessionId=" . $session->sessionId . "; path=/; secure; HttpOnly; SameSite=Lax";
                $uniqId = $id[0];
            } else {
                $uniqId = uniqid();
                $domain = "staging-ws.epower.amadeus.com";
                $name = 'ASP.NET_SessionId';
                $sessionId = "ASP.NET_SessionId=" . $uniqId . "; path=/; secure; HttpOnly; SameSite=Lax";
            }

            $search = new AmadeusSearch();
            $search->sessionId = $uniqId;
            $search->expired_at = Carbon::now()->addMinute(20);
            $search->results = ['result_list' => $list, 'search_requests' => $this->search, 'onewayCombinable' => $onewayCombinable ?? null];
            $search->save();
            lugInfo('saved search', [$search]);
            return response(view('Amadeus.search.successSearch', ['onewayCombinable' => $onewayCombinable ?? null, 'curency' => $curency, 'list' => $list, 'passengers' => [
                'adults' => $this->search['adult'],
                'childs' => $this->search['child'] ?? null,
                'infants' => $this->search['infant']  ?? null,
            ]]), 200)->withCookie(new cookie(
                $name,
                $uniqId
            ))->header('cookie', $sessionId);
        }
        return response('Unkown', 499);
    }
    
    protected function makePrice()
    {
        $adultBaseFare = rand(52, 99);
        $infantFare = rand(5, 15);
        $taxFare = 57.14;

        $pricing = [
            'adultBaseFare' => $adultBaseFare,
            'adultTotaleFare' => ($adultBaseFare + $taxFare),
            'totalbasefare' => ($this->search['adult'] * $adultBaseFare),
            'totalTotalefare' => ($this->search['adult'] * ($adultBaseFare + $taxFare)),
            'taxFare' => $taxFare,
            'curency' => $this->search['curency']
        ];

        if (isset($this->search['infant'])) {
            $pricing['infantBaseFare'] = $infantFare;
            $pricing['infantTotalFare'] = $infantFare + $taxFare;
            $pricing['totalbasefare'] = $pricing['totalbasefare']  + ($this->search['infant'] * $infantFare);
            $pricing['totalTotalefare'] = $pricing['totalTotalefare'] + ($this->search['infant'] * ($infantFare + $taxFare));
        }

        if (isset($this->search['child'])) {
            $pricing['childBaseFare'] = $adultBaseFare;
            $pricing['childTotaleFare'] = $adultBaseFare + $taxFare;
            $pricing['totalbasefare'] = $pricing['totalbasefare'] + ($this->search['child'] * $adultBaseFare);
            $pricing['totalTotalefare'] = $pricing['totalTotalefare'] + ($this->search['child'] * ($adultBaseFare + $taxFare));
        }
        return $pricing;
    }

    protected function generatePriceItinerary($isOneWayCombinable = false)
    {
        $pi = ['options' => [], 'combinations' => [], 'isOneWayCombinable' => false];
        if ($isOneWayCombinable == false) {
            $pi['pricing'] = $this->makePrice();
        }

        for ($i = 0; $i < rand(1, 10); $i++) {
            $time = Carbon::create(randomDateTime(Carbon::create($this->search['departure'])->addMinutes(10 * $i)));
            $end = Carbon::parse($time)->addHours(2);
            $option = [
                'DepartureDateTime' => $time->toIso8601String(),
                'ArrivalDateTime' => $end->toIso8601String(),
                'FlightNumber' => 'fake' . rand(400, 999),
                'DirectionId' => 0,
                'RefNumber' => $i,
                'FlightDuration' => getDiffDate($time, $end),
                'DeparturLocationCode' => $this->search['departurLocationCode'],
                'ArivelLocationCode' => $this->search['arivelLocationCode'],
            ];
            if ($isOneWayCombinable == true) {
                $option['price'] = $this->makePrice();
            }
            $pi['options'][] = $option;
        }
        if (empty($this->search['departureBack'])) {
            $pi['combinations'] = array_keys($pi['options']);
            return $pi;
        }
        $count = count($pi['options']) - 1;
        for ($j = 0; $j <= $count; $j++) {
            $time = Carbon::create(randomDateTime(Carbon::create($this->search['departureBack'])->addMinutes(10 * $j)));
            $end = Carbon::parse($time)->addHours(2);
            $option = [
                'DepartureDateTime' => $time->toIso8601String(),
                'ArrivalDateTime' => $end->toIso8601String(),
                'FlightNumber' => 'fake' . rand(400, 999),
                'DirectionId' => 1,
                'RefNumber' => $j,
                'FlightDuration' => getDiffDate($time, $end),
                'DeparturLocationCode' => $this->search['departurLocationCode'],
                'ArivelLocationCode' => $this->search['arivelLocationCode'],
            ];
            if ($isOneWayCombinable == true) {
                $option['price'] = $this->makePrice();
                $pi['isOneWayCombinable'] = true;
            }
            $pi['options'][] = $option;

            for ($k = 0; $k < $i; $k++) {
                $pi['combinations'][] = "$k;$j";
            }
        }
        return $pi;
    }

    public function ping()
    {
        extract(request()->all());
        $header = request()->header();
        if (empty($header['soapaction']) || $header['soapaction'][0] != "http://epowerv5.amadeus.com.tr/WS/Ping") {
            return response(view("Amadeus.soapActionError"), 200);
        }
        $a = '<Error Type="%s" ErrorCode="%s" ShortText="%s" Code="%s" NodeList="%s" BreakFlow="false" />';
        $validator = Validator::make(request()->all(), [
            'WSUserName' => 'required',
            'WSPassword' => 'required',
        ], [
            'WSUserName.required' => sprintf($a, 'EpowerInternalError', 'EPW.0000', 'User Name is required', 'A001', 'EPower'),
            'WSPassword.required' => sprintf($a, 'EpowerInternalError', 'EPW.0000', 'Password is required', 'A001', 'EPower'),
        ]);

        $validate1 = Validator::make(request()->all(), [
            'WSUserName' => 'bail|in:fakeAmadeus',
            'WSPassword' => 'bail|in:matinserver',
        ], [
            'in' =>  sprintf($a, 'EpowerInternalError', 'EPW.0199', 'We were unable to access your information', 'A001', 'EPower'),

        ]);

        $validate2 = Validator::make(request()->all(), [
            'WSPassword' => 'bail|in:matinserver',
        ], [
            'WSPassword.in' => sprintf($a, 'EpowerInternalError', 'EPW.0199', 'We were unable to access your information', 'A001', 'EPower'),
        ]);

        if ($validator->fails()) {
            $errors = json_decode($validator->errors(), true);
            return response(view('Amadeus.error', ['errorlist' => $errors]), 200);
        }

        if ($validate1->fails()) {
            $errors = json_decode($validate1->errors(), true);
            if (count($errors) >= 2) {
                $errors = array_splice($errors, 1);
                return response(view('Amadeus.error', ['errorlist' => $errors]), 200);
            }
            return response(view('Amadeus.error', ['errorlist' => $errors]), 200);
        }

        if ($validate2->fails()) {
            $errors = json_decode($validate2->errors(), true);
            return response(view('Amadeus.error', ['errorlist' => $errors]), 200);
        }

        if (isset($WSUserName) && isset($WSPassword)) {
            return view('Amadeus.ping.pingSuccess');
        }
    }

    public function fareRules()
    {
        extract(request()->all());
        $header = request()->header();
        if (empty($header['soapaction']) || $header['soapaction'][0] != "http://epowerv5.amadeus.com.tr/WS/GetFlightRules") {
            return response(view("Amadeus.soapActionError"), 200);
        }

        preg_match('/(?<=ASP.NET_SessionId=)[^;]+/i', $header['cookie'][0], $id);
        $session = AmadeusSearch::where('sessionId', $id[0])->where('expired_at', '>=', Carbon::now())->first();
        $a = '<Error Type="%s" ErrorCode="%s "ShortText="%s" Code="%s" NodeList="%s" BreakFlow="false" />';
        $errors1 = [];
        $errors2 = [];
        if (empty($session)) {
            return response(view('Amadeus.FareRules.error'));
        }

        if (empty($WSUserName)) {
            $errors1[] = sprintf($a, 'EpowerInternalError', 'EPW.0000', 'User Name is required', 'A001', 'EPower');
        }

        if (empty($WSPassword)) {
            $errors1[] = sprintf($a, 'EpowerInternalError', 'EPW.0000', 'Password is required', 'A001', 'EPower');
        }

        if (empty($WSUserName) && empty($WSPassword)) {
            $errors1[] = sprintf($a, 'EpowerInternalError', 'EPW.0000', 'User Name is required', 'A001', 'EPower');
            $errors1[] = sprintf($a, 'EpowerInternalError', 'EPW.0000', 'Password is required', 'A001', 'EPower');
        }

        if ($WSUserName != 'fakeAmadeus' || $WSPassword != 'matinserver') {
            $errors2[] = sprintf($a, 'EpowerInternalError', 'EPW.0199', 'We were unable to access your information', 'A001', 'EPower');
        }
        if (!empty($errors1)) {
            return response(view('Amadeus.FareRules.authValidate', ['errorlist' => $errors1]), 200);
        }
        if (!empty($errors2)) {
            return response(view('Amadeus.FareRules.authValidate', ['errorlist' => $errors2]), 200);
        }
        if (empty($errors)) {
            $departure = $session->results['result_list'][0]['options'][0]['DeparturLocationCode'];
            $arivale = $session->results['result_list'][0]['options'][0]['ArivelLocationCode'];
            return response(view('Amadeus.FareRules.success', ['departure' => $departure, 'arivale' => $arivale]), 200);
        }
    }

    public function booking()
    {
        extract(request()->all());
        lugInfo('controller', []);

        $header = request()->header();
        if (empty($header['soapaction']) || $header['soapaction'][0] != "http://epowerv5.amadeus.com.tr/WS/BookFlight") {
            return response(view("Amadeus.soapActionError"), 200);
        }
        preg_match('/(?<=ASP.NET_SessionId=)[^;]+/i', $header['cookie'][0], $id);
        $session = AmadeusSearch::where('sessionId', $id[0])->where('expired_at', '>=', Carbon::now())->first();
        $a = '<Error Type="%s" ErrorCode="%s "ShortText="%s" Code="%s" NodeList="%s" BreakFlow="false" />';
        $b = '<Error Type="%s"  "ShortText="%s" Code="%s" NodeList="%s" BreakFlow="%s" />';
        $c = '<Error Type="%s"  "ShortText="%s" Code="%s" Tag="%s"  NodeList="%s" BreakFlow="false" />';
        $sessionError = [];
        $errors = [];
        $errorsRung = [];
        if (empty($session)) {
            lugError('not found session', [$session]);
            $sessionError[] = sprintf($b, 'ValidationError',  'SearchedFlightRecommendations can not be null or empty', 'A000', 'OTA_AirBookRQ', 'true');
            return response(view('Amadeus.book.error', ['errorlist' => $sessionError]));
        }
        if ($session->expired_at < Carbon::now()) {
            $errors[] = sprintf($a, 'EpowerUnhandledError', 'EPW.0000', 'Object reference not set to an instance of an object', 'A000', 'EPower');
            return response(view('Amadeus.book.error', ['errorlist' => $errors]));
        }
        $recomendationID = $BookFlight['OTA_AirBookRQ']['@attributes']['RecommendationID'];
        $combinationID = $BookFlight['OTA_AirBookRQ']['@attributes']['CombinationID'];
        if (empty($WSUserName)) {
            $errors[] = sprintf($a, 'EpowerInternalError', 'EPW.0000', 'User Name is required', 'A001', 'EPower');
        }
        if (empty($WSPassword)) {
            $errors[] = sprintf($a, 'EpowerInternalError', 'EPW.0000', 'Password is required', 'A001', 'EPower');
        }

        if (empty($WSUserName) && empty($WSPassword)) {
            $errors[] = sprintf($a, 'EpowerInternalError', 'EPW.0000', 'User Name is required', 'A001', 'EPower');
            $errors[] = sprintf($a, 'EpowerInternalError', 'EPW.0000', 'Password is required', 'A001', 'EPower');
        }

        if ($WSUserName != 'fakeAmadeus' || $WSPassword != 'matinserver') {
            $errorsRung[] = sprintf($a, 'EpowerInternalError', 'EPW.0199', 'We were unable to access your information', 'A001', 'EPower');
        }

        if (!empty($errors)) {
            return response(view('Amadeus.book.error', ['errorlist' => $errors]), 200);
        }

        if (!empty($errorsRung)) {
            return response(view('Amadeus.book.error', ['errorlist' => $errorsRung]), 200);
        }
        // >> check passengers validate
        $search = $session->results['search_requests'];
        $child = 0;
        $infant = 0;
        if (isset($search['adult'])) {
            $adult = $search['adult'];
        }
        if (isset($search['child'])) {
            $child = $search['child'];
        }
        if (isset($search['infant'])) {
            $infant = $search['infant'];
        }
        $passengrsCount = $adult + $child  + $infant;

        $passengerType = [];
        $passInfo = [];
        $adultCount = 0;
        $childCount = 0;
        $infantCount = 0;
        if (isset($BookFlight['OTA_AirBookRQ']['TravelerInfo']['AirTraveler']['@attributes'])) {
            $passengerType = 'ADT';
            $passInfo = [
                'type' => 'ADT',
                'firstName' => $BookFlight['OTA_AirBookRQ']['TravelerInfo']['AirTraveler']['PersonName']['GivenName'],
                'namePrefix' => $BookFlight['OTA_AirBookRQ']['TravelerInfo']['AirTraveler']['PersonName']['NamePrefix'],
                'surname' => $BookFlight['OTA_AirBookRQ']['TravelerInfo']['AirTraveler']['PersonName']['Surname'],
                'count' => $infantCount + 1,
                'birthday' => $BookFlight['OTA_AirBookRQ']['TravelerInfo']['AirTraveler']['BirthDate']
            ];
        }
        if (!empty($search['originCodeBack'])) {
            if (isset($BookFlight['OTA_AirBookRQ']['@attributes']['IsOneWayCombinable'])) {
                if ($BookFlight['OTA_AirBookRQ']['@attributes']['IsOneWayCombinable'] == "true") {
                    if ($recomendationID != count($session->results['result_list']) + 1) {
                        lugError('IsOneWayCombinable is true and recomendation id not equal with count result+1', [$recomendationID, count($session->results['result_list']) + 1]);
                        $errors[] = sprintf($b, 'ValidationError',  'SearchedFlightRecommendations can not be null or empty', 'A000', 'OTA_AirBookRQ', 'true');
                        return response(view('Amadeus.book.error', ['errorlist' => $errors]), 200);
                    }
                } else {
                    if ($recomendationID > count($session->results['result_list'])) {
                        lugError('IsOneWayCombinable is not true and recomendation id is grater than count result+1', [$recomendationID, count($session->results['result_list'])]);
                        $errors[] = sprintf($b, 'ValidationError',  'SearchedFlightRecommendations can not be null or empty', 'A000', 'OTA_AirBookRQ', 'true');
                        return response(view('Amadeus.book.error', ['errorlist' => $errors]), 200);
                    }
                }
            }
        }

        if (empty($BookFlight['OTA_AirBookRQ']['TravelerInfo']['AirTraveler']['@attributes'])) {
            if (count($BookFlight['OTA_AirBookRQ']['TravelerInfo']['AirTraveler']) < $passengrsCount || count($BookFlight['OTA_AirBookRQ']['TravelerInfo']['AirTraveler']) > $passengrsCount) {
                $errors[] = sprintf($b, 'ValidationError', 'Passenger count is not equal to Searched Passenger Count', 'A001', 'OTA_AirBookRQ', 'true');
                return response(view('Amadeus.book.error', ['errorlist' => $errors]), 200);
            }
            foreach ($BookFlight['OTA_AirBookRQ']['TravelerInfo']['AirTraveler'] as $passenger) {
                switch ($passenger['@attributes']['PassengerTypeCode']) {
                    case 'ADT':
                        $passengerType[] = 'ADT';
                        $passInfo[] = [
                            'type' => 'ADT',
                            'firstName' => $passenger['PersonName']['GivenName'],
                            'namePrefix' => $passenger['PersonName']['NamePrefix'],
                            'surname' => $passenger['PersonName']['Surname'],
                            'birthday' => $passenger['BirthDate']
                        ];
                        break;
                    case 'CHD':
                        $passengerType[] = 'CHD';
                        $passInfo[] = [
                            'type' => 'CHD',
                            'firstName' => $passenger['PersonName']['GivenName'],
                            'namePrefix' => $passenger['PersonName']['NamePrefix'],
                            'surname' => $passenger['PersonName']['Surname'],
                            'birthday' => $passenger['BirthDate']
                        ];
                        break;
                    case 'INF':
                        $passengerType[] = 'INF';
                        $passInfo[] = [
                            'type' => 'INF',
                            'firstName' => $passenger['PersonName']['GivenName'],
                            'namePrefix' => $passenger['PersonName']['NamePrefix'],
                            'surname' => $passenger['PersonName']['Surname'],
                            'birthday' => $passenger['BirthDate']
                        ];
                        break;
                }


                if (empty($passenger['BirthDate'])) {
                    return response(view('Amadeus.book.birthdayError'), 200);
                }

                if (empty($passenger['PersonName']['GivenName'])) {
                    $errors[] = sprintf($c, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'Firstname', 'Passenger');
                }

                if (empty($passenger['PersonName']['NamePrefix'])) {
                    $errors[] = sprintf($c, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'Title', 'Passenger');
                }

                if (empty($passenger['PersonName']['Surname'])) {
                    $errors[] = sprintf($c, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'Surname', 'Passenger');
                }
                $passengerBirthdate = Carbon::createFromDate($passenger['BirthDate']);
                if ($passenger['@attributes']['PassengerTypeCode'] == "ADT") {
                    $passengerBirthdate = Carbon::createFromDate($passenger['BirthDate']);

                    if ($passengerBirthdate->diffInYears(Carbon::now()) < 18) {
                        $errors[] = sprintf($c, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A013', 'AdultBirthDate', 'Passenger');
                    }
                }
                if ($passenger['@attributes']['PassengerTypeCode'] == "CHD") {
                    $passengerBirthdate = Carbon::createFromDate($passenger['BirthDate']);

                    if ($passengerBirthdate->diffInYears(Carbon::now()) > 18 || $passengerBirthdate->diffInYears(Carbon::now()) < 2) {
                        $errors[] = sprintf($c, 'ValidationError', 'Validation_ThisFieldMustBeInRange', 'A013', 'ChildBirthDate', 'Passenger');
                    }
                }
                if ($passenger['@attributes']['PassengerTypeCode'] == "INF") {
                    $passengerBirthdate = Carbon::createFromDate($passenger['BirthDate']);

                    if ($passengerBirthdate->diffInYears(Carbon::now()) > 2) {
                        $errors[] = sprintf($c, 'ValidationError', 'Validation_ThisFieldMustBeInRange', 'A013', 'InfantBirthDate', 'Passenger');
                    }
                }
            }

            if (isset($search['adult']) && !in_array('ADT', $passengerType)) {
                $errors[] = sprintf($c, 'ValidationError', 'Validation_MaxLength', 'A003', 'TitleAndName', 'Passenger');
                $errors[] = sprintf($c, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'Type', 'Passenger');
                return response(view('Amadeus.book.error', ['errorlist' => $errors]), 200);
            }
            if (isset($search['infant']) && !in_array('INF', $passengerType)) {
                $errors[] = sprintf($c, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'Type', 'Passenger');
            }
        } else {
            $passenger = $BookFlight['OTA_AirBookRQ']['TravelerInfo']['AirTraveler'];
            if (empty($passenger['BirthDate'])) {
                return response(view('Amadeus.book.birthdayError'), 200);
            }

            if (empty($passenger['PersonName']['GivenName'])) {
                $errors[] = sprintf($c, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'Firstname', 'Passenger');
            }

            if (empty($passenger['PersonName']['NamePrefix'])) {
                $errors[] = sprintf($c, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'Title', 'Passenger');
            }

            if (empty($passenger['PersonName']['Surname'])) {
                $errors[] = sprintf($c, 'ValidationError', 'Validation_ThisFieldCanNotBeEmpty', 'A001', 'Surname', 'Passenger');
            }

            if ($passenger['@attributes']['PassengerTypeCode'] == "ADT") {
                $passengerBirthdate = Carbon::createFromDate($passenger['BirthDate']);

                if ($passengerBirthdate->diffInYears(Carbon::now()) < 18) {
                    $errors[] = sprintf($c, 'ValidationError', 'Validation_ThisFieldCanNotLessThan', 'A013', 'AdultBirthDate', 'Passenger');
                }
            }
        }

        if (!empty($errors)) {
            return response(view('Amadeus.book.error', ['errorlist' => $errors]), 200);
        }

        if (isset($BookFlight['OTA_AirBookRQ']['@attributes']['IsOneWayCombinable'])) {
            if ($BookFlight['OTA_AirBookRQ']['@attributes']['IsOneWayCombinable'] == "true") {
                if ($combinationID > count($session->results['onewayCombinable']['combinations'])) {
                    $errors[] = sprintf($b, 'ValidationError',  'CombinationID can not &gt;= 6', 'A001', 'OTA_AirBookRQ', 'true');
                    return response(view('Amadeus.book.error', ['errorlist' => $errors]), 200);
                }
            } else {
                if ($combinationID > (count($session->results['result_list'][$recomendationID]['combinations']))) {
                    $errors[] = sprintf($b, 'ValidationError',  'CombinationID can not &gt;= 6', 'A001', 'OTA_AirBookRQ', 'true');
                    return response(view('Amadeus.book.error', ['errorlist' => $errors]), 200);
                }
            }
        }

        if (empty($errors)) {
            if (empty($BookFlight['OTA_AirBookRQ']['TravelerInfo']['AirTraveler']['@attributes'])) {
                foreach ($passInfo as $key => $info) {
                    if ($info['type'] == 'ADT') {
                        $adultCount = $adultCount + 1;
                    }
                    if ($info['type'] == 'CHD') {
                        $childCount = $childCount + 1;
                    }
                    if ($info['type'] == 'INF') {
                        $infantCount = $infantCount + 1;
                    }
                }
            } else {
                $adultCount = 1;
            }

            $countMembers = ['adult' => $adultCount, 'child' => $childCount, 'infant' => $infantCount];
            $timeTiket = Carbon::now()->endOfDay()->toIso8601String();
            $contexId = uniqid();
            if (isset($BookFlight['OTA_AirBookRQ']['@attributes']['IsOneWayCombinable'])) {
                if ($BookFlight['OTA_AirBookRQ']['@attributes']['IsOneWayCombinable'] == "false") {
                    $pricing = $session->results['result_list'][$recomendationID]['pricing'];
                    $combinations = $session->results['result_list'][$recomendationID]['combinations'];
                    $keyBack = array_search(1, array_column($session->results['result_list'][$recomendationID]['options'], 'DirectionId'));
                    $key = array_rand(array_column($session->results['result_list'][$recomendationID]['options'], 'DirectionId'));

                    if (in_array(1, array_column($session->results['result_list'][$recomendationID]['options'], 'DirectionId')) == false) {
                        $randomOption = [$session->results['result_list'][$recomendationID]['options'][$key]];
                    }

                    if (in_array(1, array_column($session->results['result_list'][$recomendationID]['options'], 'DirectionId')) == true) {
                        $keyBack;
                        $keyGo;
                        foreach ($session->results['result_list'] as $key => $results) {
                            foreach ($results['options'] as $optionkey => $option) {
                                if ($option['DirectionId'] == 1) {
                                    $keyBack = $option['DirectionId'];
                                    break;
                                } else {
                                    $keyGo = $option['DirectionId'];
                                    break;
                                }
                            }
                        }
                        $randomOption = [$session->results['result_list'][$recomendationID]['options'][$keyGo], $session->results['result_list'][$recomendationID]['options'][$keyBack]];
                    }
                } else {
                    $randomNumber = rand(0, (count($session->results['onewayCombinable']['options']) - 1));
                    $pricing = $session->results['onewayCombinable']['options'][$randomNumber]['price'];
                    $combinations = $session->results['onewayCombinable']['combinations'];
                    $keyBack = array_search(1, array_column($session->results['onewayCombinable']['options'], 'DirectionId'));

                    $key = array_rand(array_column($session->results['onewayCombinable']['options'], 'DirectionId'));

                    if (in_array(1, array_column($session->results['onewayCombinable']['options'], 'DirectionId')) == false) {
                        $randomOption = [$session->results['onewayCombinable']['options'][$key]];
                    }

                    if (in_array(1, array_column($session->results['onewayCombinable']['options'], 'DirectionId')) == true) {
                        $keyBack;
                        $keyGo;
                        foreach ($session->results['onewayCombinable']['options'] as $key => $result) {
                            if ($result['DirectionId'] == 1) {
                                $keyBack = $result['DirectionId'];
                                break;
                            } else {
                                $keyGo = $result['DirectionId'];
                                break;
                            }
                        }
                        $randomOption = [$session->results['onewayCombinable']['options'][$keyGo], $session->results['onewayCombinable']['options'][$keyBack]];
                    }
                }
            } else {
                $random = rand(0, count($session->results['result_list']) - 1);
                $randomOption = rand(0, count($session->results['result_list'][$random]['options']) - 1);
                $pricing = $session->results['result_list'][$random]['pricing'];
                $combinations = $session->results['result_list'][$random]['combinations'];
                $randomOption = [];
                foreach ($session->results['result_list'][$random]['options'] as $key => $option) {
                    $randomOption[] = $option;
                }
            }

            $information = ['option' => $randomOption, 'pricing' => $pricing, 'combinations' => $combinations, 'timeTiket' => $timeTiket, 'contexId' => $contexId];
            $book = new AmadeusBook();
            $book->contexId = $contexId;
            $book->timeTiket = Carbon::parse($timeTiket);
            $book->amadeus_search_id = $session->id;
            $book->response = ['passInfo' => $passInfo, 'countMembers' => $countMembers, 'information' => $information];
            $book->save();
            return response(view('Amadeus.book.success', ['passInfo' => $passInfo, 'countMembers' => $countMembers, 'information' => $information]), 200);
        }
    }

    public function createTicket()
    {
        extract(request()->all());
        $header = request()->header();
        if (empty($header['soapaction']) || $header['soapaction'][0] != "http://epowerv5.amadeus.com.tr/WS/CreateTicket") {
            return response(view("Amadeus.soapActionError"), 200);
        }
        $a = '<Error Type="%s" ErrorCode="%s "ShortText="%s" Code="%s" NodeList="%s" BreakFlow="false" />';
        $b = '<Error Type="%s"  "ShortText="%s" Code="%s" NodeList="%s" BreakFlow="false" />';
        $notfound = '<Error ShortText="%s" BreakFlow="false" />';
        $errors = [];
        $errorsRung = [];
        $notfoundError = [];

        if (empty($WSUserName)) {
            $errors[] = sprintf($a, 'EpowerInternalError', 'EPW.0000', 'User Name is required', 'A001', 'EPower');
        }

        if (empty($WSPassword)) {
            $errors[] = sprintf($a, 'EpowerInternalError', 'EPW.0000', 'Password is required', 'A001', 'EPower');
        }

        if (empty($WSUserName) && empty($WSPassword)) {
            $errors[] = sprintf($a, 'EpowerInternalError', 'EPW.0000', 'User Name is required', 'A001', 'EPower');
            $errors[] = sprintf($a, 'EpowerInternalError', 'EPW.0000', 'Password is required', 'A001', 'EPower');
        }

        if ($WSUserName != 'fakeAmadeus' || $WSPassword != 'matinserver') {
            $errorsRung[] = sprintf($a, 'EpowerInternalError', 'EPW.0199', 'We were unable to access your information', 'A001', 'EPower');
        }
        if (empty($CreateTicket['OTA_AirBookRQ']['TravelerInfo']['AirTraveler']['PersonName']['Surname'])) {
            $errors[] = sprintf($b, 'ValidationError', 'Surname must be filled', 'A001', 'OTA_AirBookRQ');
        }

        if (empty($CreateTicket['OTA_AirBookRQ']['BookingReferenceID']['@attributes']['ID_Context'])) {
            $errors[] = sprintf($b, 'ValidationError', 'PNRno must be filled', 'A001', 'OTA_AirBookRQ');
        }

        if (!empty($errors)) {
            return response(view('Amadeus.createTicket.error', ['errorlist' => $errors]), 200);
        }

        if (!empty($errorsRung)) {
            return response(view('Amadeus.createTicket.error', ['errorlist' => $errorsRung]), 200);
        }

        $book = AmadeusBook::where('contexId', $CreateTicket['OTA_AirBookRQ']['BookingReferenceID']['@attributes']['ID_Context'])->first();
        $surname = "";
        if (isset($book->response['passInfo'][0])) {
            foreach ($book->response['passInfo'] as $info) {
                if ($info['type'] == 'ADT') {
                    $surname = $info['surname'];
                    break;
                }
            }
        } else {
            $surname = $book->response['passInfo']['surname'];
        }

        if (empty($book) || $CreateTicket['OTA_AirBookRQ']['TravelerInfo']['AirTraveler']['PersonName']['Surname'] != $surname) {
            $notfoundError[] = sprintf($notfound, 'PNR not found');
            return response(view('Amadeus.createTicket.error', ['errorlist' => $notfoundError]), 200);
        }

        if (Carbon::now() > $book->timeTiket) {
            $errors[] = sprintf($a, 'EpowerInternalError', 'EPW.0567', 'PNR Changed!', 'A000', 'EPower');
            return response(view('Amadeus.createTicket.error', ['errorlist' => $errors]), 200);
        }

        if (empty($errors)) {
            $response = $book->response;
            if (isset($response['passInfo'][0])) {
                foreach ($response['passInfo'] as $key => &$passenger) {
                    if ($passenger['type'] == "ADT") {
                        $passenger['ticketNumber'] = 'fakeADT' . uniqid();
                    }
                    if ($passenger['type'] == "CHD") {
                        $passenger['ticketNumber'] = 'fakeCHD' . uniqid();
                    }
                    if ($passenger['type'] == "INF") {
                        $passenger['ticketNumber'] = 'fakeINF' . uniqid();
                    }
                }
            } else {
                $response['passInfo']['ticketNumber'] = 'fakeADT' . uniqid();
            }

            $uniqId = 'fake' . uniqid();
            $domain = "staging-ws.epower.amadeus.com";
            $name = 'ASP.NET_SessionId';
            $sessionId = "ASP.NET_SessionId=" . $uniqId . "; path=/; secure; HttpOnly; SameSite=Lax";
            $ticket = new AmadeusCreateTicket();
            $ticket->session_id =  $uniqId;
            $ticket->expired_at = Carbon::now()->addMinutes(15);
            $ticket->info = [$response];
            $ticket->save();
            return response(view('Amadeus.createTicket.success', ['response' => $response]), 200)->withCookie(new cookie(
                $name,
                $uniqId
            ))->header('cookie', $sessionId);
        }
    }

    public function singOut()
    {
        extract(request()->all());
        $header = request()->header();
        if (empty($header['soapaction']) || $header['soapaction'][0] != "http://epowerv5.amadeus.com.tr/WS/SignOut") {
            return response(view("Amadeus.soapActionError"), 200);
        }
        preg_match('/(?<=ASP.NET_SessionId=)[^;]+/i', $header['cookie'][0], $sessionId);
        if (isset($sessionId)) {
            if (str_contains($sessionId[0], 'fake')) {
                $session = AmadeusCreateTicket::where('session_id', $sessionId[0])->first();
                $session->expired_at = Carbon::yesterday();
                $session->save();
            } else {
                $session = AmadeusSearch::where('sessionId', $sessionId[0])->first();
                $session->expired_at = Carbon::yesterday();
                $session->save();
            }
            return response(view("Amadeus.singOut.success"), 200);
        }
    }
}
