<?php

namespace App\Http\Controllers\Itours;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Models\AirportBuffer;
use App\Models\City;
use App\Models\ItoursAuthenticate;
use App\Models\ItoursFlightReserveByide;
use App\Models\ItoursReservePnr;
use App\Models\ItoursSearch;
use App\Models\ItoursValidateFlight;
use Carbon\Carbon;

class Itours extends Controller
{
    protected $search = [];
    public function LowFareSearch()
    {
        extract(request()->all());
        if (isset($originCodes[1])) {
            if (
                $departureDateTimes[0] < Carbon::now() || empty($departureDateTimes) ||  $departureDateTimes[1] < Carbon::now()
                || $departureDateTimes[1] < $departureDateTimes[0] || empty($departureDateTimes[1] || $adult == 0 || $child >= 9 || $airlineCode != "all")
            ) {
                lugError('error in departureDateTimes', [$departureDateTimes]);
                return response([
                    "result" => [
                        "currency" => "IRR",
                        "airTripType" => "RoundTrip",
                        "pricedItineraries" => [],
                    ],
                    "targetUrl" => null,
                    "Success" => true,
                    "error" => null,
                    "unAuthorizedRequest" => false,
                    "__abp" => true
                ]);
            }
        } else {
            if (
                $departureDateTimes[0] < Carbon::now() || empty($departureDateTimes)
                ||  $adult == 0 || $child >= 9 || $airlineCode != "all"
            ) {
                lugError('error in departureDateTime when we have one date and error in passengerscount', [$departureDateTimes, $adult]);
                return response([
                    "result" => [
                        "currency" => "IRR",
                        "airTripType" => "RoundTrip",
                        "pricedItineraries" => [],
                    ],
                    "targetUrl" => null,
                    "Success" => null,
                    "error" => [
                        "code" => -2147467261,
                        "message" => "departureDateTime is error.",
                        "details" => "",
                        "validationErrors" => null
                    ],
                    "unAuthorizedRequest" => false,
                    "__abp" => true
                ]);
            }
        }

        if (empty($originCodes) || empty($airlineCode)) {
            return response([
                "result" => null,
                "targetUrl" => null,
                "Success" => false,
                "error" => [
                    "code" => -2147467261,
                    "message" => "Object reference not set to an instance of an object.",
                    "details" => "",
                    "validationErrors" => null
                ],
                "unAuthorizedRequest" => false,
                "__abp" => true
            ]);
        }
        if (empty($destinationCodes)) {
            return response([
                "result" => null,
                "targetUrl" => null,
                "Success" => false,
                "error" => [
                    "code" => -2147467261,
                    "message" => "Object reference not set to an instance of an object.",
                    "details" => "",
                    "validationErrors" => null
                ],
                "unAuthorizedRequest" => false,
                "__abp" => true
            ]);
        }
        //>>for 2way flight
        if (!empty($originCodes[1])) {
            // lugInfo('create flight data for 2way flight', [$originCodes[1]]);
            $this->search = [
                'destinationCodesGo' => $destinationCodes[0],
                'originCodesGo' => $originCodes[0],
                'departureGo' => $departureDateTimes[0],
                'arrivalDateTimeGo' => $departureDateTimes[0],
                'adult' => $adult,
                'child' => $child,
                'infant' => $infant,
                'airTripType' => $airTripType,
                'destinationCodesBack' => $destinationCodes[1],
                'originCodesBack' => $originCodes[1],
                'departureBack' => $departureDateTimes[1],
                'arrivalDateTimeBack' => $departureDateTimes[1],

            ];
        } else { //>> for oneway
            // lugInfo('create flight data for oneway flight', [$destinationCodes]);

            $this->search = [
                'destinationCodes' => $destinationCodes[0],
                'originCodes' => $originCodes[0],
                'departure' => $departureDateTimes[0],
                'arrivalDateTime' => $departureDateTimes[0],
                'adult' => $adult,
                'child' => $child,
                'infant' => $infant,
                'airTripType' => $airTripType
            ];
        }

        $list = [];
        $beforePeyment = ['true', 'false'];
        for ($i = 0; $i <= 10; $i++) {
            $list[$i] = $this->makeSearchResponse();
            $list[$i]['key'] = 'fake' . uniqid();
            $list[$i]['paymentBeforePNR'] = $beforePeyment[array_rand($beforePeyment)];
        }

        $search = new ItoursSearch();
        $search->flightType = $this->search['airTripType'];
        $search->flight_result = $list;
        $search->save();

        return response()->json(json_decode(view('Itours.successSearch', ['list' => $list])->render(), true));
    }
    protected function makeSearchResponse()
    {
        $s = microtime(true);
        $adultBaseFare = rand(52, 99);
        $infantFare = rand(5, 15);
        $taxFare = 57.14;
        $pricing = [
            'adultBaseFare' => $adultBaseFare,
            'adultTotaleFare' => ($adultBaseFare + $taxFare),
            'totalbasefare' => ($this->search['adult'] * $adultBaseFare),
            'totalTotalefare' => ($this->search['adult'] * ($adultBaseFare + $taxFare)),
            'taxFare' => $taxFare,
            'totalTax' => $taxFare


        ];
        // lugInfo('create pricing in search', [$pricing]);
        // dd( $pricing);

        if ($this->search['infant'] != 0) {
            $pricing['infantBaseFare'] = $infantFare;
            $pricing['infantTotalFare'] = $infantFare + $taxFare;
            $pricing['totalTax'] = $pricing['totalTax'] + $taxFare;
            $pricing['totalbasefare'] = $pricing['totalbasefare']  + ($this->search['infant'] * $infantFare);
            $pricing['totalTotalefare'] = $pricing['totalTotalefare'] + (($this->search['infant'] * ($infantFare + $taxFare)));
        }
        if ($this->search['child'] != 0) {
            $pricing['childBaseFare'] = $adultBaseFare;
            $pricing['childTotaleFare'] = $adultBaseFare + $taxFare;
            $pricing['totalTax'] = $pricing['totalTax'] + $taxFare;
            $pricing['totalbasefare'] = $pricing['totalbasefare'] + ($this->search['child'] * $adultBaseFare);
            $pricing['totalTotalefare'] = $pricing['totalTotalefare'] + ($this->search['child'] * ($adultBaseFare + $taxFare));
        }

        $pi = ['options' => [], 'pricing' => $pricing, 'passengers' => [
            'adult' => $this->search['adult'],
            'child' => $this->search['child'],
            'infant' => $this->search['infant']
        ]];

        if (!empty($this->search['destinationCodesGo'])) {
            // lugInfo('flight is 2way', [$this->search]);
            $originAirportGo = Airport::where('abb', $this->search['originCodesGo'])->first();
            if (empty($originAirportGo)) {
                $origincityGo = City::where('abb', $this->search['originCodesGo'])->first();
                $originAirportGo = $origincityGo->airports->first();
                $this->search['originCodesGo'] = $originAirportGo->abb;
            }
            // lugWarning('find originAirportGo', [$originAirportGo]);
            $destinationAirportGo = Airport::where('abb', $this->search['destinationCodesGo'])->first();
            if (empty($destinationAirportGo)) {
                $destinationcityGo = City::where('abb', $this->search['destinationCodesGo'])->first();
                $destinationAirportGo = $destinationcityGo->airports->first();
                $this->search['destinationCodesGo'] = $destinationAirportGo->abb;
            }
            $originCityGo = $originAirportGo ? $originAirportGo->city->en : null;
            $originCountryGo = $originAirportGo ? $originAirportGo->city->country->en : null;
            $destinationCityGo = $destinationAirportGo ? $destinationAirportGo->city->en : null;
            $destinationCountryGo = $destinationAirportGo ? $destinationAirportGo->city->country->en : null;
            // back
            $originAirportBack = Airport::where('abb', $this->search['originCodesBack'])->first();
            if (empty($originAirportBack)) {
                $origincity = City::where('abb', $this->search['originCodesBack'])->first();
                $originAirportBack = $origincity->airports->first();
                $this->search['originCodesBack'] = $originAirportBack->abb;
            }
            // lugWarning('find originAirportBack', [$originAirportBack]);

            $destinationAirportBack = Airport::where('abb', $this->search['destinationCodesBack'])->first();
            if (empty($destinationAirportBack)) {
                $destinationcity = City::where('abb', $this->search['destinationCodesBack'])->first();
                $destinationAirportBack = $destinationcity->airports->first();
                $this->search['destinationCodesBack'] = $destinationAirportBack->abb;
            }
            // lugWarning('find destinationAirportBack', [$destinationAirportBack]);

            $originCityBack = $originAirportBack ? $originAirportBack->city->en : null;
            $originCountryBack = $originAirportBack ? $originAirportBack->city->country->en : null;
            $destinationCityBack = $destinationAirportBack ? $destinationAirportBack->city->en : null;
            $destinationCountryBack = $destinationAirportBack ? $destinationAirportBack->city->country->en : null;
            for ($i = 0; $i < 1; $i++) {
                $timeGo = Carbon::create(randomDateTime(Carbon::create($this->search['departureGo'])->addMinutes(10 * $i)));
                $endGo = Carbon::parse($timeGo)->addHours(2);
                $timeBack = Carbon::create(randomDateTime(Carbon::create($this->search['departureBack'])->addMinutes(10 * $i)));
                $endBack = Carbon::parse($timeBack)->addHours(2);
                $pi['options'][] = [
                    'DepartureDateTimeGo' => $timeGo->toIso8601String(),
                    'ArrivalDateTimeGo' => $endGo->toIso8601String(),
                    'FlightNumberGo' => 'fake' . rand(400, 999),
                    'RefNumberGo' => $i,
                    'FlightDurationGo' => $timeGo->diff($endGo)->format('%H:%I:%S'),
                    'DeparturLocationCodeGo' => $this->search['originCodesGo'],
                    'ArivelLocationCodeGo' => $this->search['destinationCodesGo'],
                    'originAirportGo' => $originAirportGo ? $originAirportGo->en : null,
                    'destinationAirportGo' => $destinationAirportGo ? $destinationAirportGo->en : null,
                    'originCityGo' => $originCityGo,
                    'originCountryGo' => $originCountryGo,
                    'destinationCityGo' => $destinationCityGo,
                    'destinationCountryGo' => $destinationCountryGo,
                    // back
                    'DepartureDateTimeBack' => $timeBack->toIso8601String(),
                    'ArrivalDateTimeBack' => $endBack->toIso8601String(),
                    'FlightNumberBack' => 'fake' . rand(400, 999),
                    'RefNumberBack' => $i,
                    'FlightDurationBack' => $timeBack->diff($endBack)->format('%H:%I:%S'),
                    'DeparturLocationCodeBack' => $this->search['originCodesBack'],
                    'ArivelLocationCodeBack' => $this->search['destinationCodesBack'],
                    'originAirportBack' => $originAirportBack ? $originAirportBack->en : null,
                    'destinationAirportBack' => $destinationAirportBack ? $destinationAirportBack->en : null,
                    'originCityBack' => $originCityBack,
                    'originCountryBack' => $originCountryBack,
                    'destinationCityBack' => $destinationCityBack,
                    'destinationCountryBack' => $destinationCountryBack,
                ];
            }
            $t = microtime(true) - $s;
            // lugWarning('time for search in oneway', [$s, $t]);
            return $pi;
        }

        // lugInfo('flight is oneway');
        $originAirport = Airport::where('abb', $this->search['originCodes'])->first();
        if (empty($originAirport)) {
            $origincity = City::where('abb', $this->search['originCodes'])->first();
            $originAirport = $origincity->airports->first();
            $this->search['originCodes'] = $originAirport->abb;
        }

        // lugWarning('find originAirport', [$originAirport, $this->search['originCodes'], $this->search['destinationCodes']]);
        $destinationAirport = Airport::where('abb', $this->search['destinationCodes'])->first();
        if (empty($destinationAirport)) {
            $destinationcity = City::where('abb', $this->search['destinationCodes'])->first();
            $destinationAirport = $destinationcity->airports->first();
            $this->search['destinationCodes'] = $destinationAirport->abb;
        }

        // lugWarning('find destinationAirport', [$destinationAirport]);
        $originCity = $originAirport ? $originAirport->city->en : null;
        $originCountry = $originAirport ? $originAirport->city->country->en : null;
        $destinationCity = $destinationAirport ? $destinationAirport->city->en : null;
        $destinationCountry = $destinationAirport ? $destinationAirport->city->country->en : null;

        for ($i = 0; $i < 1; $i++) {
            $time = Carbon::create(randomDateTime(Carbon::create($this->search['departure'])->addMinutes(10 * $i)));
            $end = Carbon::parse($time)->addHours(2);
            $pi['options'][] = [
                'DepartureDateTime' => $time->toIso8601String(),
                'ArrivalDateTime' => $end->toIso8601String(),
                'FlightNumber' => 'fake' . rand(400, 999),
                'DirectionId' => 0,
                'RefNumber' => $i,
                'FlightDuration' => $time->diff($end)->format('%H:%I:%S'),
                'DeparturLocationCode' => $this->search['originCodes'],
                'ArivelLocationCode' => $this->search['destinationCodes'],
                'originAirport' => $originAirport ? $originAirport->en : null,
                'destinationAirport' => $destinationAirport ? $destinationAirport->en : null,
                'originCity' => $originCity,
                'originCountry' => $originCountry,
                'destinationCity' => $destinationCity,
                'destinationCountry' => $destinationCountry,
            ];
        }
        $t = microtime(true) - $s;
        // lugWarning('time for search in 2way', [$s, $t]);
        return $pi;
    }

    public function getFlightRules()
    {
        extract(request()->all());
        $results = ItoursSearch::pluck('flight_result')->toArray();
        $result = [];
        foreach ($results as $resultKey => $searches) {
            foreach ($searches as $searchKey => $search) {
                if ($key == $search['key']) {
                    $result = ['search' => $search, 'searchKey' => $searchKey, 'resultKey' => $resultKey, 'key' => $key];
                    break;
                }
            }
        }
        if (empty($result)) {
            return response([
                "result" => null,
                "targetUrl" => null,
                "Success" => false,
                "error" => [
                    "code" => -2146233033,
                    "message" => "Input string was not in a correct format.",
                    "details" => "",
                    "validationErrors" => null
                ],
                "unAuthorizedRequest" => false,
                "__abp" => true
            ]);
        }

        if (!empty($result)) {
            return response()->json(json_decode(view('Itours.FareRules.sucessFareRules'), true));
        }
    }

    public function validateFlight()
    {
        extract(request()->all());
        $results = ItoursSearch::pluck('flight_result')->toArray();
        $result = [];
        foreach ($results as $resultKey => $searches) {

            foreach ($searches as $searchKey => $search) {
                if ($key == $search['key']) {
                    $result = ['search' => $search, 'searchKey' => $searchKey, 'resultKey' => $resultKey, 'key' => $key];
                    $tripType = ItoursSearch::where('id', $resultKey)->pluck('flightType')->first();
                    break;
                }
            }
        }
        if (empty($result)) {
            lugError('ItoursSearch cant find');
            return response([
                "result" => null,
                "targetUrl" => null,
                "Success" => false,
                "error" => [
                    "code" => -2146233033,
                    "message" => "Input string was not in a correct format.",
                    "details" => "",
                    "validationErrors" => null
                ],
                "unAuthorizedRequest" => false,
                "__abp" => true
            ]);
        }
        // $x = ItoursSearch::whereJsonContains('flight_result',  [['key' => $result['key']]])->get();
        $result['search']['provider'] = 'TravelFusion';
        if (!empty($result)) {
            $validated = new ItoursValidateFlight();
            $validated->flightType = $tripType;
            $validated->key = $result['search']['key'];
            $validated->details = $result['search'];
            $validated->save();

            // lugInfo('result for validate', [$result]);
            return response()->json(json_decode(view("Itours.Validate.sucessValidateFlight", ['result' => $result['search'], 'tripType' => $tripType])->render(), true));
        }
    }

    public function reservePNR()
    {
        extract(request()->all());

        if (empty($key)) {
            return response([
                "result" => null,
                "targetUrl" => null,
                "Success" => false,
                "error" => [
                    "code" => -2146233033,
                    "message" => "key string was not in a correct format.",
                    "details" => "",
                    "validationErrors" => null
                ],
                "unAuthorizedRequest" => false,
                "__abp" => true
            ]);
        }
        if (!empty($key)) {
            $pnr = "fakePnr" . uniqId();
            $validate = ItoursValidateFlight::where('key', $key)->first();
            $info = $validate->details;
            if (count($passengers) != $info['passengers']['adult'] + $info['passengers']['child'] + $info['passengers']['infant']) {
                return response([
                    "result" => null,
                    "targetUrl" => null,
                    "Success" => false,
                    "error" => [
                        "code" => -2147467261,
                        "message" => "passengers count is not equal with search.",
                        "details" => "",
                        "validationErrors" => null
                    ],
                    "unAuthorizedRequest" => false,
                    "__abp" => true
                ]);
            }
            $reserved = new ItoursReservePnr();
            $reserved->pnr = $pnr;
            $reserved->key = $key;
            $reserved->provider = $info['provider'];
            $reserved->validate_id = $validate->id;
            $reserved->request = ['reserver' => $reserver, 'passengers' => $passengers, 'searchInfo' => $info, 'flightTrip' => $validate->flightType];
            $reserved->save();
            return response([
                "result" => $pnr,
                "targetUrl" => null,
                "success" => true,
                "error" => null,
                "unAuthorizedRequest" => false,
                "__abp" => true,
            ], 200);
        }
    }

    public function GetFlightReserveById()
    {
        extract(request()->all());
        if (empty($reserveId)) {
            return response([
                "result" => null,
                "targetUrl" => null,
                "Success" => false,
                "error" => [
                    "code" => -2146233033,
                    "message" => "key string was not in a correct format.",
                    "details" => "",
                    "validationErrors" => null
                ],
                "unAuthorizedRequest" => false,
                "__abp" => true
            ]);
        }
        $reserved = ItoursReservePnr::where('pnr', $reserveId)->first();
        if(empty($reserved)){
            lugError('reservepnr is empty',[$reservId]);
            return response([
                "result" => null,
                "targetUrl" => null,
                "Success" => false,
                "error" => [
                    "code" => -2146233033,
                    "message" => "this pnrcode not found ,please do reserve pnr first",
                    "details" => "",
                    "validationErrors" => null
                ],
                "unAuthorizedRequest" => false,
                "__abp" => true
            ]);
        }
        $counts = $reserved->request['searchInfo']['passengers']['adult'] + $reserved->request['searchInfo']['passengers']['child'] + $reserved->request['searchInfo']['passengers']['infant'];
        $basefare[0] = [
            "passengerTypeQuantity" => [
                "code" => "ADT",
                "quantity" => 1,
            ],
        ];
        if ($reserved->request['searchInfo']['passengers']['child'] != 0) {
            $child = $reserved->request['searchInfo']['passengers']['child'];
            $basefare[1] = [
                "passengerTypeQuantity" => [
                    "code" => "CHD",
                    "quantity" => $reserved->request['searchInfo']['passengers']['child'],
                ]
            ];
        }
        if ($reserved->request['searchInfo']['passengers']['infant'] != 0) {
            $basefare[2] = [
                "passengerTypeQuantity" => [
                    "code" => "INF",
                    "quantity" => $reserved->request['searchInfo']['passengers']['infant'],
                ]
            ];
        }
        $reserver = $reserved->request['reserver'];
        $passengers = $reserved->request['passengers'];
        $pnrcode = $reserved->pnr;
        $passengers = [];

        foreach ($reserved->request['passengers'] as $passenger) {
            $passengers[] = [
                "firstName" => $passenger['firstName'],
                "lastName" => $passenger['lastName'],
                "title" => $passenger['gender'],
                "code" => $passenger['code'],
                "ticketNumber" => "fakeTicket" . uniqId(),
                "passportNumber" => "295",
                "passportExpireDate" => Carbon::now()->addYears(10)->format('Y-m-d'),
                "birthDate" => $passenger['birthDate'],
                "passengerId" => 39678,
                "nationality" => $passenger['nationalityCode'],
            ];
        }

        $originDestinationOptions = [];
        if (!empty($reserved->request['searchInfo']['options'][0]['FlightNumberBack'])) {
            $originDestinationOptions[] = [
                0 => [
                    "journeyDuration" => $reserved->request['searchInfo']['options'][0]['ArrivalDateTimeGo'],
                    "numberOfStop" => 0,
                    "flightSegments" =>  [
                        0 => [
                            "flightNumber" => $reserved->request['searchInfo']['options'][0]['FlightNumberGo'],
                            "departureDateTime" => $reserved->request['searchInfo']['options'][0]['DepartureDateTimeGo'],
                            "arrivalDateTime" => $reserved->request['searchInfo']['options'][0]['ArrivalDateTimeGo'],
                            "resBookDesigCode" => "Y",
                            "arrivalAirport" => [
                                "locationCode" => $reserved->request['searchInfo']['options'][0]['ArivelLocationCodeGo'],
                                "terminalID" => null,
                                "locationName" => $reserved->request['searchInfo']['options'][0]['destinationAirportGo'],
                                "countryName" => $reserved->request['searchInfo']['options'][0]['destinationCountryGo'] ?? null,
                                "cityName" => $reserved->request['searchInfo']['options'][0]['destinationCityGo'],
                            ],
                            "departureAirport" =>  [
                                "locationCode" => $reserved->request['searchInfo']['options'][0]['DeparturLocationCodeGo'],
                                "terminalID" => null,
                                "locationName" => $reserved->request['searchInfo']['options'][0]['originAirportGo'],
                                "countryName" => $reserved->request['searchInfo']['options'][0]['originCountryGo'] ?? null,
                                "cityName" => $reserved->request['searchInfo']['options'][0]['originCityGo'] ?? null,
                            ],
                            "marketingAirline" =>  [
                                "code" => "U2",
                                "name" => "Easyjet Airline Company Limited",
                                "photoUrl" => "https://cdn3.itours.no/Content/images/airlines/Thumbs/Easyjet Airline Company.png",
                            ],
                            "operatingAirline" =>  [
                                "code" => "U2",
                                "name" => "Easyjet Airline Company Limited",
                                "photoUrl" => "https://cdn3.itours.no/Content/images/airlines/Thumbs/Easyjet Airline Company.png",
                            ],
                            "airEquipType" => null,
                            "statusCode" => null,
                            "flightDuration" => "02:00:00",
                            "fareBasis" =>  [
                                0 =>  [
                                    "fareBasisCode" => null,
                                    "bookingCode" => null,
                                    "passengerType" => " ",
                                ]
                            ],
                            "baggageInformation" =>  [
                                0 =>  [
                                    "baggageAllowance" => 0,
                                    "unitType" => "P",
                                    "passengerType" => "ADT",
                                ]
                            ],
                            "extraBaggageInformation" => null,
                            "handBagInformation" => null,
                            "cabinClass" => [
                                "name" => "Economy",
                                "code" => "Economic",
                            ],
                            "isOutbound" => true,
                            "stopTime" => "00:00:00",
                        ]
                    ]
                ], 1 => [
                    "journeyDuration" => $reserved->request['searchInfo']['options'][0]['FlightDurationBack'],
                    "numberOfStop" => 0,
                    "flightSegments" =>  [
                        0 => [
                            "flightNumber" => $reserved->request['searchInfo']['options'][0]['FlightNumberBack'],
                            "departureDateTime" => $reserved->request['searchInfo']['options'][0]['DepartureDateTimeBack'],
                            "arrivalDateTime" => $reserved->request['searchInfo']['options'][0]['ArrivalDateTimeBack'],
                            "resBookDesigCode" => "Y",
                            "arrivalAirport" => [
                                "locationCode" => $reserved->request['searchInfo']['options'][0]['ArivelLocationCodeBack'],
                                "terminalID" => null,
                                "locationName" => $reserved->request['searchInfo']['options'][0]['destinationAirportBack'],
                                "countryName" => $reserved->request['searchInfo']['options'][0]['destinationCountryBack'] ?? null,
                                "cityName" => $reserved->request['searchInfo']['options'][0]['destinationCityBack'],
                            ],
                            "departureAirport" =>  [
                                "locationCode" => $reserved->request['searchInfo']['options'][0]['DeparturLocationCodeBack'],
                                "terminalID" => null,
                                "locationName" => $reserved->request['searchInfo']['options'][0]['originAirportBack'],
                                "countryName" => $reserved->request['searchInfo']['options'][0]['originCountryBack'] ?? null,
                                "cityName" => $reserved->request['searchInfo']['options'][0]['originCityBack'] ?? null,
                            ],
                            "marketingAirline" =>  [
                                "code" => "U2",
                                "name" => "Easyjet Airline Company Limited",
                                "photoUrl" => "https://cdn3.itours.no/Content/images/airlines/Thumbs/Easyjet Airline Company.png",
                            ],
                            "operatingAirline" =>  [
                                "code" => "U2",
                                "name" => "Easyjet Airline Company Limited",
                                "photoUrl" => "https://cdn3.itours.no/Content/images/airlines/Thumbs/Easyjet Airline Company.png",
                            ],
                            "airEquipType" => null,
                            "statusCode" => null,
                            "flightDuration" => "02:00:00",
                            "fareBasis" =>  [
                                0 =>  [
                                    "fareBasisCode" => null,
                                    "bookingCode" => null,
                                    "passengerType" => " ",
                                ]
                            ],
                            "baggageInformation" =>  [
                                0 =>  [
                                    "baggageAllowance" => 0,
                                    "unitType" => "P",
                                    "passengerType" => "ADT",
                                ]
                            ],
                            "extraBaggageInformation" => null,
                            "handBagInformation" => null,
                            "cabinClass" => [
                                "name" => "Economy",
                                "code" => "Economic",
                            ],
                            "isOutbound" => true,
                            "stopTime" => "00:00:00",
                        ]
                    ]
                ]
            ];
        } else {
            $originDestinationOptions[] = [
                "journeyDuration" => $reserved->request['searchInfo']['options'][0]['ArrivalDateTime'],
                "numberOfStop" => 0,
                "flightSegments" =>  [
                    0 => [
                        "flightNumber" => $reserved->request['searchInfo']['options'][0]['FlightNumber'],
                        "departureDateTime" => $reserved->request['searchInfo']['options'][0]['DepartureDateTime'],
                        "arrivalDateTime" => $reserved->request['searchInfo']['options'][0]['ArrivalDateTime'],
                        "resBookDesigCode" => "Y",
                        "arrivalAirport" => [
                            "locationCode" => $reserved->request['searchInfo']['options'][0]['ArivelLocationCode'],
                            "terminalID" => null,
                            "locationName" => $reserved->request['searchInfo']['options'][0]['destinationAirport'],
                            "countryName" => $reserved->request['searchInfo']['options'][0]['destinationCountry'] ?? null,
                            "cityName" => $reserved->request['searchInfo']['options'][0]['destinationCity'],
                        ],
                        "departureAirport" =>  [
                            "locationCode" => $reserved->request['searchInfo']['options'][0]['DeparturLocationCode'],
                            "terminalID" => null,
                            "locationName" => $reserved->request['searchInfo']['options'][0]['originAirport'],
                            "countryName" => $reserved->request['searchInfo']['options'][0]['originCountry'] ?? null,
                            "cityName" => $reserved->request['searchInfo']['options'][0]['originCity'] ?? null,
                        ],
                        "marketingAirline" =>  [
                            "code" => "U2",
                            "name" => "Easyjet Airline Company Limited",
                            "photoUrl" => "https://cdn3.itours.no/Content/images/airlines/Thumbs/Easyjet Airline Company.png",
                        ],
                        "operatingAirline" =>  [
                            "code" => "U2",
                            "name" => "Easyjet Airline Company Limited",
                            "photoUrl" => "https://cdn3.itours.no/Content/images/airlines/Thumbs/Easyjet Airline Company.png",
                        ],
                        "airEquipType" => null,
                        "statusCode" => null,
                        "flightDuration" => "02:00:00",
                        "fareBasis" =>  [
                            0 =>  [
                                "fareBasisCode" => null,
                                "bookingCode" => null,
                                "passengerType" => " ",
                            ]
                        ],
                        "baggageInformation" =>  [
                            0 =>  [
                                "baggageAllowance" => 0,
                                "unitType" => "P",
                                "passengerType" => "ADT",
                            ]
                        ],
                        "extraBaggageInformation" => null,
                        "handBagInformation" => null,
                        "cabinClass" => [
                            "name" => "Economy",
                            "code" => "Economic",
                        ],
                        "isOutbound" => true,
                        "stopTime" => "00:00:00",
                    ]
                ]
            ];
        }
        $flightReserved = new ItoursFlightReserveByide();
        $flightReserved->pnrCode = $reserved->pnr;
        $flightReserved->providerName = $reserved->provider;
        $flightReserved->username = $reserver['username'];
        $flightReserved->reserve_id = $reserved->id;
        $flightReserved->flight_detail = $originDestinationOptions;
        $flightReserved->save();
        // lugInfo('saved information in ItoursFlightReserveByide table',[$flightReserved]);
        return response(
            [
                "result" => [
                    "reserveStatus" => "Issued",
                    "pnrCode" => "$reserved->pnr",
                    "pnrId" => 26766,
                    "bookedDateTime" => Carbon::now(),
                    "airItinerary" => [
                        "originDestinationOptions" =>  [
                            $originDestinationOptions
                        ]
                    ],
                    "passengers" =>  $passengers,
                    "reserver" =>  $reserved->request['reserver'],
                    "providerName"=> "Sabre",
                    "passengersFare" => [
                        $basefare,
                        "fare" => [
                            "baseFare" => $reserved->request['searchInfo']['pricing']['totalbasefare'],
                            "totalTaxes" =>  $reserved->request['searchInfo']['pricing']['taxFare'],
                            "totalFare" =>  $reserved->request['searchInfo']['pricing']['totalTotalefare'],
                            "totalFee" => 0.0,
                            "extraBaggage" => 0.0,
                            "totalFareWithExtraBaggage" => 0.0,
                        ]
                    ],
                    "paymentBeforePNR" => true,
                    "isTicketed" => false,
                    "isDomestic" => false,
                    "isBookNowRequested" => false,
                    "isOutSidePNR" => false,
                    "airTripType" => $reserved->request['flightTrip'],
                    "pnrStatus" => "Issued",
                    "hasExtraBaggage" => false,
                ],
                "targetUrl" => null,
                "success" => true,
                "error" => null,
                "unAuthorizedRequest" => false,
                "__abp" => true,
            ],
            200
        );
    }

    public function getPnrDetails()
    {
        extract(request()->all());
        $data = request()->all();
        $selected = ItoursFlightReserveByide::where('pnrCode', $data['PNRCode'])->first();
        if (empty($selected)) {
            return response([
                "result" => null,
                "targetUrl" => null,
                "success" => false,
                "error" =>  [
                    "code" => 0,
                    "message" => "Current user did not login to the application!",
                    "details" => null,
                    "validationErrors" => null,
                ],
                "unAuthorizedRequest" => true,
                "__abp" => true
            ]);
        }

        $reserved = ItoursReservePnr::where('pnr', $selected->pnrCode)->first();
        if (empty($reserved)) {
            lugError('ItoursReservePnr not find');
            return response([
                "result" => null,
                "targetUrl" => null,
                "success" => false,
                "error" =>  [
                    "code" => 0,
                    "message" => "Current pnr code not find in reservepnr!",
                    "details" => null,
                    "validationErrors" => null,
                ],
                "unAuthorizedRequest" => true,
                "__abp" => true
            ]);
        }
        $pricing = $reserved->request['searchInfo']['pricing'];
        $passengersCount = $reserved->request['searchInfo']['passengers'];
        $passengers = $reserved->request['passengers'];
        $result = ['pnrDetails' => ['pnr' => $reserved['pnr'], 'key' => $reserved['key']], 'options' => $reserved->request['searchInfo']['options'][0], 'pricing' => $reserved->request['searchInfo']['pricing'], 'passengers' => $reserved->request['passengers'], 'reserver' => $reserved->request['reserver'], 'passengerCount' => $reserved->request['searchInfo']['passengers']];
        $x=json_decode(view('Itours.GetPnrDetails.successResponse', ['result' => $result])->render(), true);
        // lugInfo('result for detail pnr',[$result,json_last_error_msg(),$x]);
        return response()->json(json_decode(view('Itours.GetPnrDetails.successResponse', ['result' => $result])->render(), true));
    }

    public function Authenticate()
    {
        extract(request()->all());
        $data = request()->all();
        $accessToken = 'fakeAccessToken' . uniqId();
        $refreshToken = "fakeToken" . uniqId();
        $encryptedAccessToken = app('hash')->make($accessToken);

        $auth = new ItoursAuthenticate();
        $auth->accessToken = $accessToken;
        $auth->username = $data['usernameOrEmailAddress'];
        $auth->password = $data['password'];
        $auth->expireInSeconds = '2077200';
        $auth->save();
        // lugInfo('auth in database saved', [$auth]);
        return response([
            "result" => [
                "accessToken" => $accessToken,
                "refreshToken" => $refreshToken,
                "encryptedAccessToken" => $encryptedAccessToken,
                "expireInSeconds" => 2077200,
                "user" => [
                    "userName" => $data['usernameOrEmailAddress'],
                    "emailAddress" => $data['usernameOrEmailAddress'],
                    "displayName" => $data['usernameOrEmailAddress'],
                    "firstName" => "",
                    "lastName" => "",
                    "isActive" => true,
                    "gender" => false,
                    "birthDay" => null,
                    "isNewsLater" => false,
                    "isEmailConfirmed" => false,
                    "nationalityId" => null,
                    "phoneNumber" => null,
                    "roleNames" =>  [
                        0 => "Affiliate",
                    ],
                    "id" => 20644,
                ]
            ],
            "targetUrl" => null,
            "success" => true,
            "error" => null,
            "unAuthorizedRequest" => false,
            "__abp" => true
        ], 200);
    }

    public function confirmByDEposit()
    {
        extract(request()->all());
        $data = request()->all();
        $token = request()->header()['authorization'][0];
        // lugWarning('token is ', [$token, $data]);
        $validToken = ItoursAuthenticate::where('accessToken', substr($token, 7))->first();
        // lugWarning('validToken is ', [$validToken]);

        if (empty($validToken)) {
            lugError('empty Validation token in itoursAuthenticate database', [$token]);
            return response([
                "result" => null,
                "targetUrl" => null,
                "success" => false,
                "error" =>  [
                    "code" => 4004,
                    "message" => "NotAccess",
                    "details" => null,
                    "validationErrors" => null,
                ],
                "unAuthorizedRequest" => false,
                "__abp" => true
            ]);
        }

        if (empty($data['reserveId'])) {
            lugError('empty reserveId in request', [$data]);
            return response([
                "result" => null,
                "targetUrl" => null,
                "success" => false,
                "error" =>  [
                    "code" => 4006,
                    "message" => "CreditLimit",
                    "details" => null,
                    "validationErrors" => null,
                ],
                "unAuthorizedRequest" => false,
                "__abp" => true
            ]);
        }

        if (isset($validToken) && !empty($data) && !empty($data['reserveId'])) {
            return response([
                "result" => true,
                "targetUrl" => null,
                "success" => true,
                "error" => null,
                "unAuthorizedRequest" => false,
                "__abp" => true,
            ], 200);
        }
    }

    public function getDirectTicketById()
    {
        extract(request()->all());
        $data = request()->all();
        $flight = ItoursReservePnr::where('pnr', $data['reserveId'])->first();
        if (empty($flight)) {
            return response([
                "result" => 0,
                "targetUrl" => null,
                "success" => false,
                "error" => [
                    'error' => 'this pnr code not find'
                ],
                "unAuthorizedRequest" => false,
                "__abp" => true,
            ]);
        }
        if (!empty($data) && !empty($flight)) {
            return response([
                "result" => 0,
                "targetUrl" => null,
                "success" => true,
                "error" => null,
                "unAuthorizedRequest" => false,
                "__abp" => true,
            ], 200);
        }
    }

    public function pricePnr()
    {
        extract(request()->all());
        $data = request()->all();
        $token = request()->header()['authorization'][0];
        $validToken = ItoursAuthenticate::where('accessToken', substr($token, 6))->first();
        // lugInfo('token in price is', [substr($token, 6)]);

        $flight = ItoursReservePnr::where('key', $data['key'])->first();
        if (empty($flight)) {
            lugError('flight(reservepnr) not find', [$data['key']]);
            return response([
                "result" => null,
                "targetUrl" => null,
                "success" => false,
                "error" => [
                    "code" => 0,
                    "message" => "Object reference not set to an instance of an object.",
                    "details" => null,
                    "validationErrors" => null,
                ],
                "unAuthorizedRequest" => false,
                "__abp" => true,
            ]);
        }
        $reserved = ItoursFlightReserveByide::where('pnrCode', $flight->pnr)->first();
        if (empty($reserved)) {
            lugError('reserved(ItoursFlightReserveByide) not find', [$flight]);
            return response([
                "result" => null,
                "targetUrl" => null,
                "success" => false,
                "error" => [
                    "code" => 0,
                    "message" => "Source sequence doesn't contain any elements",
                    "details" => null,
                    "validationErrors" => null,
                ],
                "unAuthorizedRequest" => false,
                "__abp" => true,
            ]);
        }
        $passengersCount = $flight->request['searchInfo']['passengers'];
        $passengers = $flight->request['passengers'];
        $pricing = $flight->request['searchInfo']['pricing'];
        $flightInfo = ($reserved->flight_detail);
        return response()->json(json_decode(view('Itours.PricePnr.successPnr', ['resultFlight' => ['flightKey' => $flight->key, 'pnrCode' => $flight->pnr], 'flightInfo' => $flightInfo, 'passengersCount' => $passengersCount, 'passengers' => $passengers, 'pricing' => $pricing, 'reserver' => $flight->request['reserver']])->render(), true));
    }

    public function issuePnr()
    {
        extract(request()->all());
        $token = request()->header()['authorization'][0];
        $validToken = ItoursAuthenticate::where('accessToken', substr($token, 6))->first();
        // lugInfo('token in issue is', [substr($token, 6)]);
        $reserved = ItoursFlightReserveByide::where('pnrCode', request()->all()['reserveId'])->first();
        if (empty($reserved)) {
            lugError('reserved(ItoursFlightReserveByide) not find', [request()->all()['reserveId']]);
            return response([
                "result" => null,
                "targetUrl" => null,
                "success" => false,
                "error" =>  [
                    "code" => 0,
                    "message" => "Object reference not set to an instance of an object.",
                    "details" => null,
                    "validationErrors" => null,
                ],
                "unAuthorizedRequest" => false,
                "__abp" => true,
            ]);
        }
        $flight = ItoursReservePnr::where('id', $reserved->reserve_id)->first();
        if (empty($flight)) {
            lugError('flight(reservepnr) not find', [$reserved]);
            return response([
                "result" => null,
                "targetUrl" => null,
                "success" => false,
                "error" => [
                    "code" => 0,
                    "message" => "PaymentNotSuccessful",
                    "details" => null,
                    "validationErrors" => null,
                ],
                "unAuthorizedRequest" => false,
                "__abp" => true,
            ]);
        }
        $pricing = $flight->request['searchInfo']['pricing'];
        $passengersCount = $flight->request['searchInfo']['passengers'];
        $flightInfo = ($reserved->flight_detail);

        $passengers = $flight->request['passengers'];
        $tickets = [];
        foreach ($passengers as $key => $passenger) {
            $tickets[] = 'fake' . uniqid();
        }

        // lugInfo('result for issue is', [$passengersCount, $flightInfo, $passengers, $pricing, $flight]);
        // lugInfo('result issue pnr', [$flightInfo, $validToken]);
        return response()->json(json_decode(view('Itours.IssuePnr.suceessIssue', ['resultFlight' => ['flightKey' => $flight->key, 'pnrCode' => $flight->pnr], 'flightInfo' => $flightInfo, 'passengersCount' => $passengersCount, 'passengers' => $passengers, 'pricing' => $pricing, 'reserver' => $flight->request['reserver'], 'tickets' => $tickets])->render(), true));
    }
}
