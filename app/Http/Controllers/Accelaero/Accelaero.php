<?php

namespace App\Http\Controllers\Accelaero;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Parto\Session;
use App\Models\AccelaeroBooking;
use App\Models\AccelaeroPrice;
use App\Models\AccelaeroSearch;
use App\Models\AccelaeroSelectedFlight;
use App\Models\Session as ModelsSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie as FacadesCookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use SimpleXMLElement;

class Accelaero extends Controller
{
    public function RetriveFlightAvailability()
    {
        extract(request()->all());

        ///> check uername and passwrd
        if (
            empty($wsse__Security['wsse__UsernameToken']['wsse__Username']) || empty($wsse__Security['wsse__UsernameToken']['wsse__Password']) ||
            $wsse__Security['wsse__UsernameToken']['wsse__Password'] != '1s@secret' ||
            $wsse__Security['wsse__UsernameToken']['wsse__Username'] != "FLYBABYLON"
        ) {
            $type = 'ERR';
            $code = 320;
            $error = 'Invalid login value';
            return  view('Accelaero/Error', compact(['error', 'code', 'type']));
        }

        ///> login credentioals
        $username = $wsse__Security['wsse__UsernameToken']['wsse__Username'];
        $password = $wsse__Security['wsse__UsernameToken']['wsse__Password'];

        ///> cehck adult count
        $passengerAvail = $ns__OTA_AirAvailRQ['ns__TravelerInfoSummary']['ns__AirTravelerAvail'];
        $AdultCount = 0;
        $InfantCount = 0;
        $ChildCount = 0;
        if (isset($passengerAvail['ns__PassengerTypeQuantity'])) {
            if (isset($passengerAvail['ns__PassengerTypeQuantity']['@attributes'])) {
                if ($passengerAvail['ns__PassengerTypeQuantity']['@attributes']['Code'] == 'ADT') {
                    $AdultCount = $passengerAvail['ns__PassengerTypeQuantity']['@attributes']['Quantity'];
                } else if ($passengerAvail['ns__PassengerTypeQuantity']['@attributes']['Code'] == 'CHD') {
                    $ChildCount = $passengerAvail['ns__PassengerTypeQuantity']['@attributes']['Quantity'];;
                } else if ($passengerAvail['ns__PassengerTypeQuantity']['@attributes']['Code'] == 'INF') {
                    $InfantCount = $passengerAvail['ns__PassengerTypeQuantity']['@attributes']['Quantity'];
                }
            } else {
                foreach ($passengerAvail['ns__PassengerTypeQuantity'] as $in) {
                    if ($in['@attributes']['Code'] == 'ADT') {
                        $AdultCount = $in['@attributes']['Quantity'];
                    } else if ($in['@attributes']['Code'] == 'CHD') {
                        $ChildCount = $in['@attributes']['Quantity'];
                    } else if ($in['@attributes']['Code'] == 'INF') {
                        $InfantCount = $in['@attributes']['Quantity'];
                    }
                }
            }
        } else {
            if ($InfantCount > $AdultCount) {
                $type = "ERR";
                $error = "Invalid value";
                $code = "320";
                return view('Accelaero/Error', compact(['error', 'code', 'type']));
            }
            $type = "ERR";
            $error = "Invalid number of adults";
            $code = "397";
            return view('Accelaero/Error', compact(['error', 'code', 'type']));
        }

        ///> TripType
        if (isset($ns__OTA_AirAvailRQ['ns__OriginDestinationInformation']['ns__DepartureDateTime'])) {
            ///> TripType = OneWay
            $TripType = "OneWay";
            $DepartureDateTime = $ns__OTA_AirAvailRQ['ns__OriginDestinationInformation']['ns__DepartureDateTime'];
            $OriginCode = $ns__OTA_AirAvailRQ['ns__OriginDestinationInformation']['ns__OriginLocation']['@attributes']['LocationCode'];
            $DestinationCode = $ns__OTA_AirAvailRQ['ns__OriginDestinationInformation']['ns__DestinationLocation']['@attributes']['LocationCode'];
        } else if (isset($ns__OTA_AirAvailRQ['ns__OriginDestinationInformation'][0]) && $ns__OTA_AirAvailRQ['ns__OriginDestinationInformation'][0]['ns__OriginLocation']['@attributes']['LocationCode'] == $ns__OTA_AirAvailRQ['ns__OriginDestinationInformation'][1]['ns__DestinationLocation']['@attributes']['LocationCode']) {
            ///> TripType = Return
            $TripType = "Return";
            $DepartureDateTime = $ns__OTA_AirAvailRQ['ns__OriginDestinationInformation'][0]['ns__DepartureDateTime'];
            $OriginCode = $ns__OTA_AirAvailRQ['ns__OriginDestinationInformation'][0]['ns__OriginLocation']['@attributes']['LocationCode'];
            $DestinationCode = $ns__OTA_AirAvailRQ['ns__OriginDestinationInformation'][0]['ns__DestinationLocation']['@attributes']['LocationCode'];
        } else {
            $type = "ERR";
            $error = "Invalid value";
            $code = "320";
            return view('Accelaero/Error', compact(['error', 'code', 'type']));
        }

        ///> search flight with given params
        $checkFlight = AccelaeroSearch::where('OriginCode', $OriginCode)->where('DestinationCode', $DestinationCode)->where('DepratureDateTime', $DepartureDateTime)->where('TripType', $TripType)->where('AdultCount', $AdultCount)->where('ChildCount', $ChildCount)->where('InfantCount', $InfantCount)->first();
        if (is_null($checkFlight)) {
            $type = "ERR";
            $error = "No Availability";
            $code = "322";
            return view('Accelaero/Error', compact(['error', 'code', 'type']));
        }
        
        ///> store selected flight in db
        $convert = response($checkFlight->responses)->header('Content-Type', 'application/xml');
        $xml = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $convert->getContent());
        $xml = preg_replace('/(>\s+<)/', '><', $xml);
        $responses = json_decode(json_encode(simplexml_load_string($xml)), TRUE);
        
        $OriginDestinationOption = $responses['soap__Body']['ns1__OTA_AirAvailRS']['ns1__OriginDestinationInformation']['ns1__OriginDestinationOptions']['ns1__OriginDestinationOption'];
        $AlreadyExists = AccelaeroSelectedFlight::where('username',$username)->where('password',$password)->where('FlightId',$checkFlight->id)->first();
        if(is_null($AlreadyExists)) {
            $selected = new AccelaeroSelectedFlight();
            $selected->username = $username;
            $selected->password = $password;
            $selected->FlightId = $checkFlight->id;
            $selected->DepartureDateTime = $checkFlight->DepratureDateTime;
            $selected->ArrivalDateTime = $checkFlight->ArrivalDateTime;
            $selected->OriginDestinationOption = json_encode($OriginDestinationOption);
            $selected->save();
        }
        
        // return response($checkFlight->responses,200)->header('Content-Type', 'application/xml');
        
        ///> this is for testing in testing-master folder
        return $responses;
    }

    public function GetPriceQuote()
    {
        extract(request()->all());

        ///> check uername and passwrd
        if (
            empty($wsse__Security['wsse__UsernameToken']['wsse__Username']) || empty($wsse__Security['wsse__UsernameToken']['wsse__Password']) ||
            $wsse__Security['wsse__UsernameToken']['wsse__Password'] != '1s@secret' ||
            $wsse__Security['wsse__UsernameToken']['wsse__Username'] != "FLYBABYLON"
        ) {
            $type = 'ERR';
            $code = 320;
            $error = 'Invalid login value';
            return  view('Accelaero/Error', compact(['error', 'code', 'type']));
        }
        
        ///> login credentioals
        $username = $wsse__Security['wsse__UsernameToken']['wsse__Username'];
        $password = $wsse__Security['wsse__UsernameToken']['wsse__Password'];

        ///> get selected flight informations
        $LastSelectedFlight = AccelaeroSelectedFlight::where('username',$username)->where('password',$password)->orderByDesc('id')->first();
        if(is_null($LastSelectedFlight)) {
            $type = "ERR";
            $code = "320";
            $error = "Invalid flight";

            return view('Accelaero/Error', compact(['code', 'type', 'error']));
        }
        
        $CheckFlightInfo = false;
        $FlightSegment = json_decode($LastSelectedFlight->OriginDestinationOption,true);

        if(isset($FlightSegment[0])) {
            foreach($FlightSegment as $Flight) {
                if(($Flight['ns1__FlightSegment']['@attributes']['RPH'] == $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption']['ns2__FlightSegment']['@attributes']['RPH']) && ($Flight['ns1__FlightSegment']['@attributes']['FlightNumber'] == $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption']['ns2__FlightSegment']['@attributes']['FlightNumber'])) {
                    $CheckFlightInfo = true;
                }
            }
        } else {
            if(($FlightSegment['ns1__FlightSegment']['@attributes']['RPH'] == $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption']['ns2__FlightSegment']['@attributes']['RPH']) && ($FlightSegment['ns1__FlightSegment']['@attributes']['FlightNumber'] == $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption']['ns2__FlightSegment']['@attributes']['FlightNumber'])) {
                $CheckFlightInfo = true;
            }
        }

        ///> check flight number and RPH
        if(!$CheckFlightInfo) {
            $type = "ERR";
            $code = "320";
            $error = "Invalid flight number";

            return view('Accelaero/Error', compact(['code', 'type', 'error']));
        }

        $AdultCount = 0;
        $ChildCount = 0;
        $InfantCount = 0;

        ///> check request
        if (empty($ns2__OTA_AirPriceRQ['ns2__AirItinerary']['@attributes']['DirectionInd']) && empty($ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']) && empty($ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption'])) {
            $type = "ERR";
            $code = "320";
            $error = "Invalid value";

            return view('Accelaero/Error', compact(['code', 'type', 'error']));
        }
        if (empty($ns2__OTA_AirPriceRQ['ns2__TravelerInfoSummary']) || empty($ns2__OTA_AirPriceRQ['ns2__TravelerInfoSummary']['ns2__AirTravelerAvail'])) {
            $type = "ERR";
            $code = "397";
            $error = "Invalid number of adults";

            return view('Accelaero/Error', compact(['code', 'type', 'error']));
        }
        if (isset($ns2__OTA_AirPriceRQ['ns2__TravelerInfoSummary']['ns2__AirTravelerAvail']['ns2__PassengerTypeQuantity']['@attributes'])) {
            ///> just one passenger
            if ($ns2__OTA_AirPriceRQ['ns2__TravelerInfoSummary']['ns2__AirTravelerAvail']['ns2__PassengerTypeQuantity']['@attributes']['Code'] == "ADT" && $ns2__OTA_AirPriceRQ['ns2__TravelerInfoSummary']['ns2__AirTravelerAvail']['ns2__PassengerTypeQuantity']['@attributes']['Quantity'] >= 1) {
                $AdultCount = $ns2__OTA_AirPriceRQ['ns2__TravelerInfoSummary']['ns2__AirTravelerAvail']['ns2__PassengerTypeQuantity']['@attributes']['Quantity'];
            } else {
                $type = "ERR";
                $code = "397";
                $error = "Invalid number of adults";

                return view('Accelaero/Error', compact(['code', 'type', 'error']));
            }
        }
        if (isset($ns2__OTA_AirPriceRQ['ns2__TravelerInfoSummary']['ns2__AirTravelerAvail']['ns2__PassengerTypeQuantity'][0])) {
            ///> more than one passenger
            foreach ($ns2__OTA_AirPriceRQ['ns2__TravelerInfoSummary']['ns2__AirTravelerAvail']['ns2__PassengerTypeQuantity'] as $passenger) {
                if ($passenger['@attributes']['Code'] == 'ADT') {
                    $AdultCount = $passenger['@attributes']['Quantity'];
                } else if ($passenger['@attributes']['Code'] == 'CHD') {
                    $ChildCount = $passenger['@attributes']['Quantity'];
                } else if ($passenger['@attributes']['Code'] == 'INF') {
                    $InfantCount = $passenger['@attributes']['Quantity'];
                }
                if ($AdultCount == 0) {
                    $type = "ERR";
                    $code = "397";
                    $error = "Invalid number of adults";

                    return view('Accelaero/Error', compact(['code', 'type', 'error']));
                }
            }
        }

        ///> get Trip infos base on TripType
        $TripType = $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['@attributes']['DirectionInd'];
        if ($TripType == 'OneWay' && isset($ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption']['ns2__FlightSegment'])) {
            ///> oneWay -  direct , non-stop
            $DepartureDateTime = $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption']['ns2__FlightSegment']['@attributes']['DepartureDateTime'];
            $FlightNumber = $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption']['ns2__FlightSegment']['@attributes']['FlightNumber'];
            $RPH = $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption']['ns2__FlightSegment']['@attributes']['RPH'];
            $OriginCode = $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption']['ns2__FlightSegment']['ns2__DepartureAirport']['@attributes']['LocationCode'];
            $DestinationCode = $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption']['ns2__FlightSegment']['ns2__ArrivalAirport']['@attributes']['LocationCode'];
        }
        if ($TripType == 'OneWay' && isset($ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption'][0])) {
            ///> OneWay - multi segment
            $DepartureDateTime = $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption'][0]['ns2__FlightSegment']['@attributes']['DepartureDateTime'];
            $FlightNumber = $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption'][0]['ns2__FlightSegment']['@attributes']['FlightNumber'];
            $OriginCode = $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption'][0]['ns2__FlightSegment']['ns2__DepartureAirport']['@attributes']['LocationCode'];
            $DestinationCode = $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption'][count($ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption']) - 1]['ns2__FlightSegment']['ns2__ArrivalAirport']['@attributes']['LocationCode'];
        }
        if ($TripType == 'Return' && isset($ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption'][0])) {
            $DepartureDateTime = $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption'][0]['ns2__FlightSegment']['@attributes']['DepartureDateTime'];
            $FlightNumber = $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption'][0]['ns2__FlightSegment']['@attributes']['FlightNumber'];
            $OriginCode = $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption'][0]['ns2__FlightSegment']['ns2__DepartureAirport']['@attributes']['LocationCode'];
            $DestinationCode = $ns2__OTA_AirPriceRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption'][0]['ns2__FlightSegment']['ns2__ArrivalAirport']['@attributes']['LocationCode'];
        }

        ///> search for flight then show it's price
        $checkFlight = AccelaeroPrice::where('OriginCode', $OriginCode)->where('DestinationCode', $DestinationCode)->where('DepratureDateTime', $DepartureDateTime)->where('TripType', $TripType)->where('AdultCount', $AdultCount)->where('ChildCount', $ChildCount)->where('InfantCount', $InfantCount)->first();
        if (is_null($checkFlight)) {
            $type = 'ERR';
            $code = 322;
            $error = 'No Availability';
            return  view('Accelaero/Error', compact(['error', 'code', 'type']));
        }

        $Flight =  json_decode(AccelaeroSearch::where('id',$LastSelectedFlight->FlightId)->first(['OriginDestination'])['OriginDestination'],true);
        $Bundle = $Flight['ns1__OTA_AirAvailRS']['ns1__AAAirAvailRSExt']['ns1__PricedItineraries']['ns1__PricedItinerary']['ns1__AirItinerary']['ns1__OriginDestinationOptions']['ns1__AABundledServiceExt'];
        $option = $Flight['ns1__OTA_AirAvailRS']['ns1__AAAirAvailRSExt']['ns1__PricedItineraries']['ns1__PricedItinerary']['ns1__AirItinerary']['ns1__OriginDestinationOptions']['ns1__OriginDestinationOption'];
        $PricingInfo = $Flight['ns1__OTA_AirAvailRS']['ns1__AAAirAvailRSExt']['ns1__PricedItineraries']['ns1__PricedItinerary']['ns1__AirItineraryPricingInfo'];

        // return view('Accelaero.Price',compact(['Bundle','PricingInfo','option']));
    
        ///> this is for testing in testing-master folder
        $convert = response($checkFlight->responses)->header('Content-Type', 'application/xml');
        $xml = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $convert->getContent());
        $xml = preg_replace('/(>\s+<)/', '><', $xml);
        $responses = json_decode(json_encode(simplexml_load_string($xml)), TRUE);
        return $responses;
    }

    public function Booking()
    {
        extract(request()->all());
        ///> check username, password
        if (
            empty($wsse__Security['wsse__UsernameToken']['wsse__Username']) || empty($wsse__Security['wsse__UsernameToken']['wsse__Password']) ||
            $wsse__Security['wsse__UsernameToken']['wsse__Password'] != '1s@secret' ||
            $wsse__Security['wsse__UsernameToken']['wsse__Username'] != "FLYBABYLON"
        ) {
            $type = 'ERR';
            $code = 320;
            $error = 'Invalid login value';
            return  view('Accelaero/Error', compact(['error', 'code', 'type']));
        } else {
            $username = $wsse__Security['wsse__UsernameToken']['wsse__Username'];
            $password = $wsse__Security['wsse__UsernameToken']['wsse__Password'];
        }

        ///> check request
        if (empty($ns2__OTA_AirBookRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']) && empty(empty($ns2__OTA_AirBookRQ['ns2__TravelerInfo']) && (empty($AAAirBookRQExt) || empty($AAAirBookRQExt['ContactInfo'])))) {
            $type = "AAE";
            $code = "29";
            $error = "Insufficient agent available credit";
            return  view('Accelaero/Error', compact(['error', 'code', 'type']));
        }

        ///> check flight segment
        $options = $ns2__OTA_AirBookRQ['ns2__AirItinerary']['ns2__OriginDestinationOptions']['ns2__OriginDestinationOption'];
        if (isset($options['ns2__FlightSegment'])) {
            ///> OneWay
            $TripType = "OneWay";
            if (isset($options['ns2__FlightSegment']['@attributes'])) {
                ///> one segment
                $DepartureDateTime = $options['ns2__FlightSegment']['@attributes']['DepartureDateTime'];
                $OriginCode = $options['ns2__FlightSegment']['ns2__DepartureAirport']['@attributes']['LocationCode'];
                $DestinationCode = $options['ns2__FlightSegment']['ns2__ArrivalAirport']['@attributes']['LocationCode'];
            } else {
                ///> multi segment
                $DepartureDateTime  = $options['ns2__FlightSegment'][0]['@attributes']['DepartureDateTime'];
                $OriginCode = $options['ns2__FlightSegment'][1]['ns2__DepartureAirport']['@attributes']['LocationCode'];
                $DestinationCode = $options['ns2__FlightSegment'][0]['ns2__ArrivalAirport']['@attributes']['LocationCode'];
            }
        }
        if (isset($options[0])) {
            ///> Return
            $TripType = "Return";
            if (isset($options[0]['ns2__FlightSegment']['@attributes'])) {
                $DepartureDateTime = $options[0]['ns2__FlightSegment']['@attributes']['DepartureDateTime'];
                $OriginCode = $options[0]['ns2__FlightSegment']['ns2__DepartureAirport']['@attributes']['LocationCode'];
                $DestinationCode = $options[0]['ns2__FlightSegment']['ns2__ArrivalAirport']['@attributes']['LocationCode'];
            } else {
                ///> multi segment
                $DepartureDateTime = $options[0]['ns2__FlightSegment'][1]['@attributes']['DepartureDateTime'];
                $OriginCode = $options[0]['ns2__FlightSegment'][1]['ns2__DepartureAirport']['@attributes']['LocationCode'];
                $DestinationCode = $options[0]['ns2__FlightSegment'][0]['ns2__ArrivalAirport']['@attributes']['LocationCode'];
            }
        }

        ///> check passengers type and quantity
        $AdultCount = 0;
        $ChildCount = 0;
        $InfantCount = 0;
        if (isset($ns2__OTA_AirBookRQ['ns2__TravelerInfo']['ns2__AirTraveler']['@attributes'])) {
            ///> just one passenger
            if ($ns2__OTA_AirBookRQ['ns2__TravelerInfo']['ns2__AirTraveler']['@attributes']['PassengerTypeCode'] != 'ADT') {
                $type = "ERR";
                $code = 397;
                $error = "Invalid number of adults";
                return  view('Accelaero/Error', compact(['error', 'code', 'type']));
            }
            $AdultCount = 1;
        }
        if (isset($ns2__OTA_AirBookRQ['ns2__TravelerInfo']['ns2__AirTraveler'][0])) {
            $passengers = $ns2__OTA_AirBookRQ['ns2__TravelerInfo']['ns2__AirTraveler'];
            foreach ($passengers as $passenger) {
                if ($passenger['@attributes']['PassengerTypeCode'] == "ADT") {
                    $AdultCount++;
                } else if ($passenger['@attributes']['PassengerTypeCode'] == "CHD") {
                    $ChildCount++;
                } else if ($passenger['@attributes']['PassengerTypeCode'] == "INF") {
                    $InfantCount++;
                }
            }

            if (($AdultCount == 0) || $InfantCount > $AdultCount) {
                $type = "ERR";
                $code = 397;
                $error = "Invalid number of adults";
                return  view('Accelaero/Error', compact(['error', 'code', 'type']));
            }
        }
        $CountOfTravelerInfo = count($ns2__OTA_AirBookRQ['ns2__TravelerInfo']['ns2__AirTraveler']);
        $AllPassCount = $AdultCount + $ChildCount + $InfantCount;
        if ($CountOfTravelerInfo != $AllPassCount) {
            $type = "ERR";
            $code = 320;
            $error = "Invalid value";
            return  view('Accelaero/Error', compact(['error', 'code', 'type']));
        }

        ///> check flight
        $checkFlight = AccelaeroSearch::where('OriginCode', $OriginCode)->where('DestinationCode', $DestinationCode)->where('DepratureDateTime', $DepartureDateTime)->where('TripType', $TripType)->where('AdultCount', $AdultCount)->where('ChildCount', $ChildCount)->where('InfantCount', $InfantCount)->where('TripType', $TripType)->first();
        if (is_null($checkFlight)) {
            $type = "ERR";
            $code = 320;
            $error = "Invalid flight value";
            return  view('Accelaero/Error', compact(['error', 'code', 'type']));
        }

        ///> check flight amount
        $res = json_decode($checkFlight->OriginDestination, true);
        $AmountEntered = $ns2__OTA_AirBookRQ['ns2__Fulfillment']['ns2__PaymentDetails']['ns2__PaymentDetail']['ns2__PaymentAmount']['@attributes']['Amount'];
        $AmountFromDB = $res['ns1__OTA_AirAvailRS']['ns1__AAAirAvailRSExt']['ns1__PricedItineraries']['ns1__PricedItinerary']['ns1__AirItineraryPricingInfo']['ns1__PTC_FareBreakdowns']['ns1__PTC_FareBreakdown']['ns1__PassengerFare']['ns1__TotalFare']['@attributes']['Amount'];
        if ($AmountEntered != $AmountFromDB) {
            $type = "ERR";
            $code = 320;
            $error = "Invalid amount value";
            return  view('Accelaero/Error', compact(['error', 'code', 'type']));
        }
        $PriceInfo = $res['ns1__OTA_AirAvailRS']['ns1__AAAirAvailRSExt']['ns1__PricedItineraries']['ns1__PricedItinerary']['ns1__AirItineraryPricingInfo'];

        ///> create information for displaying
        $TicketNo = [];
        if (isset($ns2__OTA_AirBookRQ['ns2__TravelerInfo']['ns2__AirTraveler']['@attributes'])) {
            $val = ['TicketNo' => rand(00000000000000, 9999999999999)];
            $TicketNo = $val;
        } else {
            foreach ($ns2__OTA_AirBookRQ['ns2__TravelerInfo']['ns2__AirTraveler'] as $traveler) {
                $val = ['TicketNo' => rand(00000000000000, 9999999999999)];
                $TicketNo[] = $val;
            }
        }
        $TicketInfo = [
            'TicketNo' => $TicketNo,
            'FlightSegment' => "$OriginCode/$DestinationCode",
            'BookingReferenceID' => rand(11111111, 99999999),
        ];


        ///> store in db
        $response = ['OriginDestination' => $options, 'Fullfillment' => $ns2__OTA_AirBookRQ['ns2__Fulfillment']['ns2__PaymentDetails']['ns2__PaymentDetail'], 'ContactInfo' => $AAAirBookRQExt['ContactInfo'], 'TravelerInfo' => $ns2__OTA_AirBookRQ['ns2__TravelerInfo'], 'TicketInfo' => $TicketInfo, 'PriceInfo' => $PriceInfo, 'PassCount' => ['Adult' => $AdultCount, 'Child' => $ChildCount, 'Infant' => $InfantCount]];

        // $booking = new AccelaeroBooking();
        // $booking->username = $username;
        // $booking->password = $password;
        // $booking->BookingReferenceID = $TicketInfo['BookingReferenceID'];
        // $booking->response = json_encode($response);
        // $booking->save();
        return response(view('Accelaero.successBooking', ['OriginDestination' => $options, 'Amount' => $AmountEntered, 'ContactInfo' => $AAAirBookRQExt['ContactInfo'], 'TravelerInfo' => $ns2__OTA_AirBookRQ['ns2__TravelerInfo'], 'TicketInfo' => $TicketInfo, 'AirTraveler' => $ns2__OTA_AirBookRQ['ns2__TravelerInfo'], 'PriceInfo' => $PriceInfo, 'PassCount' => ['Adult' => $AdultCount, 'Child' => $ChildCount, 'Infant' => $InfantCount]]), 200);
    }

    public function BaggageDetail()
    {
        $xml = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', request()->getContent());
        $xml = preg_replace('/(>\s+<)/', '><', $xml);
        $array = json_decode(json_encode(simplexml_load_string($xml)), TRUE);

        ///> check username, password
        if (
            empty($array['soapenv__Header']['wsse__Security']['wsse__UsernameToken']['wsse__Username']) || empty($array['soapenv__Header']['wsse__Security']['wsse__UsernameToken']['wsse__Password']) ||
            $array['soapenv__Header']['wsse__Security']['wsse__UsernameToken']['wsse__Password'] != '1s@secret' ||
            $array['soapenv__Header']['wsse__Security']['wsse__UsernameToken']['wsse__Username'] != "FLYBABYLON"
        ) {
            $type = 'ERR';
            $code = 320;
            $error = 'Invalid login value';
            return  view('Accelaero/Error', compact(['error', 'code', 'type']));
        } else {
            $username = $array['soapenv__Header']['wsse__Security']['wsse__UsernameToken']['wsse__Username'];
            $password = $array['soapenv__Header']['wsse__Security']['wsse__UsernameToken']['wsse__Password'];
        }

        $flights = $array['soapenv__Body']['ns__AA_OTA_AirBaggageDetailsRQ']['ns__BaggageDetailsRequests']['ns__BaggageDetailsRequest'];
        if (isset($flights['ns__FlightSegmentInfo'])) {
            ///> one flight
            $DepartureDateTime = $flights['ns__FlightSegmentInfo']['@attributes']['DepartureDateTime'];
            $ArrivalDateTime = $flights['ns__FlightSegmentInfo']['@attributes']['ArrivalDateTime'];
            $RPH = $flights['ns__FlightSegmentInfo']['@attributes']['RPH'];
            $OriginCode = $flights['ns__FlightSegmentInfo']['ns__DepartureAirport']['@attributes']['LocationCode'];
            $DestinationCode = $flights['ns__FlightSegmentInfo']['ns__ArrivalAirport']['@attributes']['LocationCode'];
        }
        if (isset($flights[0])) {
            ///> multi segment flight
        }

        ///> check flight
        $CheckFlight = AccelaeroSearch::where('DepratureDateTime', $DepartureDateTime)->where('OriginCode', $OriginCode)->where('DestinationCode', $DestinationCode)->where('RPH', $RPH)->where('ArrivalDateTime', $ArrivalDateTime)->first();
        if (is_null($CheckFlight)) {
            $type = "ERR";
            $error = "No Availability";
            $code = "322";
            return view('Accelaero/Error', compact(['error', 'code', 'type']));
        }

        $res = json_decode($CheckFlight->OriginDestination, true);
        return $res;
    }
}
