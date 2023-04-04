<?php

namespace App\Http\Controllers\Itours;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Models\City;
use App\Models\ItoursFlightReserveByide;
use App\Models\ItoursReservePnr;
use App\Models\ItoursSearch;
use App\Models\ItoursValidateFlight;
use App\Models\NewItoursAuth;
use App\Models\NewItoursAuthe;
use App\Models\NewItoursAvailibility;
use App\Models\NewItoursReserve;
use Carbon\Carbon;

class NewItours extends Controller
{
    public $search = [];
    public function Authenticate()
    {
        $data = request()->all();
        $accessToken = 'fakeAccessToken' . uniqId();
        $auth = new NewItoursAuthe();
        $auth->accessToken = $accessToken;
        $auth->username = $data['username'];
        $auth->password = $data['password'];
        $auth->currency = $data['currency'];
        $auth->apiKey = $data['apiKey'];
        $auth->tenantId = $data['tenantId'];
        $auth->expireInSeconds = Carbon::now()->addSeconds(1800);
        $auth->save();
        return response([
            "success" => true,
            "result" => [
                "accessToken" => $accessToken,
                "expireInSeconds" => 1800,

            ],
            "error" => null
        ], 200);
    }

    public function Availability()
    {
        extract(request()->all());
        $header = request()->header();
        // dd(substr($header['authorization'][0],7));

        if (isset($departureDates[1]) || $airTripType == "roundTrip") {
            if ($departureDates[0] < Carbon::now() || empty($departureDates) || empty($originCodes[0]) || empty($originCodes[1]) || empty($destinationCodes[0]) || empty($destinationCodes[1])) {
                lugError('error in departureDateTimes', [$departureDates]);
                return response([
                    "success" => false,
                    "result" => null,
                    "error" => [
                        "code" => 5010,
                        "message" => "Unknown error has occurred"
                    ]
                ]);
            }
            $token = NewItoursAuthe::where('accessToken', substr($header['authorization'][0], 7))->where('expireInSeconds', '>', Carbon::now())->first();
            if (!isset($token)) {
                return response([], 401);
            }
            $key = "fakeItoursFlightKey" . uniqid();
            $this->search = [
                'destinationCodesGo' => $destinationCodes[0],
                'originCodesGo' => $originCodes[0],
                'departureGo' => $departureDates[0],
                'arrivalDateTimeGo' => $departureDates[0],
                'adult' => $adult,
                'child' => $child,
                'infant' => $infant,
                'airTripType' => $airTripType,
                'destinationCodesBack' => $destinationCodes[1],
                'originCodesBack' => $originCodes[1],
                'departureBack' => $departureDates[1],
                'arrivalDateTimeBack' => $departureDates[1],
                'accessToken' => $header['authorization'][0],
                'currency' => $token->currency


            ];
        } else {
            $this->search = [
                'destinationCodes' => $destinationCodes[0],
                'originCodes' => $originCodes[0],
                'departure' => $departureDates[0],
                'arrivalDateTime' => $departureDates[0],
                'adult' => $adult,
                'child' => $child,
                'infant' => $infant,
                'airTripType' => $airTripType,
                'accessToken' => $header['authorization'][0],
                'currency' => $token->currency
            ];
        }

        $search = new NewItoursAvailibility();
        $search->flight_type = $this->search['airTripType'];
        $search->flight_request = $this->search;
        $search->flight_key = $key;
        $search->save();

        return response([
            "success" => true,
            "result" => [
                "key" => $key
            ],
            "error" => null
        ]);
    }

    public function getAvailibility()
    {
        extract(request()->all());
        $header = request()->header();
        $token = NewItoursAuthe::where('accessToken', substr($header['authorization'][0], 7))->where('expireInSeconds', '>', Carbon::now())->first();
        if (!isset($token)) {
            return response([], 401);
        }
        if (empty($key)) {
            return response([
                "type" => "https://tools.ietf.org/html/rfc7231#section-6.5.1",
                "title" => "One or more validation errors occurred.",
                "status" => 400,
                "traceId" => "|c923893f-4b0a87d8326455bf.",
                "errors" => [
                    "key" => [
                        "The key field is required."
                    ]
                ]
            ]);
        }
        $searchKey = NewItoursAvailibility::where('flight_key', $key)->first();
        $this->search = $searchKey->flight_request;

        if (empty($searchKey)) {
            return response([
                "success" => false,
                "result" => null,
                "error" => [
                    "code" => 5010,
                    "message" => "Unknown error has occurred"
                ]
            ]);
        }
        $list = [];
        for ($i = 0; $i <= 10; $i++) {
            $list[$i] = $this->makeSearchResponse();
            $list[$i]['key'] = 'fakeFlightAvilableKey' . uniqid();
        }
        $avilable = new ItoursSearch();
        $avilable->flightType = $this->search['airTripType'];
        $avilable->flight_result = $list;
        $avilable->save();
        return response()->json(json_decode(view('Itours.NewItours.Availibility.getAvailibility', ['list' => $list, 'count' => count($list), 'currency' => $this->search['currency'], 'tripType' => $this->search['airTripType']])->render(), true));
    }

    protected function makeSearchResponse()
    {
        $s = microtime(true);
        if ($this->search['currency'] === "IRR") {
            $adultBaseFare = rand(33699260.00, 88699260.00);
            $infantFare = rand(13699260.00, 23699260.00);
            $taxFare = 22742000.00;
        } else {
            $adultBaseFare = rand(52, 99);
            $infantFare = rand(5, 15);
            $taxFare = 57.14;
        }

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
        $header = request()->header();
        $token = NewItoursAuthe::where('accessToken', substr($header['authorization'][0], 7))->where('expireInSeconds', '>', Carbon::now())->first();
        if (!isset($token)) {
            return response([], 401);
        }

        if (empty($key)) {
            return response([
                "type" => "https://tools.ietf.org/html/rfc7231#section-6.5.1",
                "title" => "One or more validation errors occurred.",
                "status" => 400,
                "traceId" => "|c923893f-4b0a87d8326455bf.",
                "errors" => [
                    "key" => [
                        "The key field is required."
                    ]
                ]
            ]);
        }
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
                "success" => false,
                "result" => null,
                "error" => [
                    "code" => 5010,
                    "message" => "Unknown error has occurred"
                ]
            ]);
        }
        return response([
            "success" => true,
            "result" => [
                "flightRules" => [
                    [
                        "flightRule1" => [
                            [
                                "ruleTitle" => "Extra info",
                                "ruleDescription" => "Maybe a transient visa is needed for flights with stop"
                            ]
                        ],
                        "fareBase" => null
                    ]
                ]
            ],
            "error" => null
        ]);
    }

    public function validateFlight()
    {
        extract(request()->all());
        $header = request()->header();
        $token = NewItoursAuthe::where('accessToken', substr($header['authorization'][0], 7))->where('expireInSeconds', '>', Carbon::now())->first();
        if (!isset($token)) {
            return response([], 401);
        }
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
            return response([
                "success" => false,
                "result" => null,
                "error" => [
                    "code" => 5010,
                    "message" => "Unknown error has occurred"
                ]
            ]);
        }
        $result['search']['provider'] = 'TravelFusion';
        if (!empty($result)) {
            $validated = new ItoursValidateFlight();
            $validated->flightType = $tripType;
            $validated->key = $result['search']['key'];
            $validated->details = $result['search'];
            $validated->save();
            // lugInfo('result for validate', [$result]);
            return response()->json(json_decode(view("Itours.NewItours.Validate.successValidate", ['result' => $result['search'], 'tripType' => $tripType, 'currency' => $token->currency])->render(), true));
        }
    }

    public function reserve()
    {
        extract(request()->all());
        $header = request()->header();
        $token = NewItoursAuthe::where('accessToken', substr($header['authorization'][0], 7))->where('expireInSeconds', '>', Carbon::now())->first();
        if (!isset($token)) {
            return response([], 401);
        }

        $validate = ItoursValidateFlight::where('key', $key)->first();
        $info = $validate->details;
        $pnr = "fakePnr" . uniqId();
        $reservId = 'fakeId' . uniqId();

        if (count($passengers) != $info['passengers']['adult'] + $info['passengers']['child'] + $info['passengers']['infant']) {
            return response([
                "success" => false,
                "result" => null,
                "error" => [
                    "code" => 5010,
                    "message" => "Unknown error has occurred"
                ]
            ]);
        }
        $counts = $info['passengers']['adult'] + $info['passengers']['child'] + $info['passengers']['infant'];
        $basefare[0] = [
            "passengerTypeQuantity" => [
                "code" => "ADT",
                "quantity" => 1,
            ],
        ];
        if ($info['passengers']['child'] != 0) {
            $child = $info['passengers']['child'];
            $basefare[1] = [
                "passengerTypeQuantity" => [
                    "code" => "CHD",
                    "quantity" => $info['passengers']['child'],
                ]
            ];
        }
        if ($info['passengers']['infant'] != 0) {
            $basefare[2] = [
                "passengerTypeQuantity" => [
                    "code" => "INF",
                    "quantity" => $info['passengers']['infant'],
                ]
            ];
        }


        $originDestinationOptions = [];
        if (!empty($info['options'][0]['FlightNumberBack'])) {
            $expiration = Carbon::parse($info['options'][0]['DepartureDateTimeGo'])->subDay(1)->format('Y-m-d H:i');
            $originDestinationOptions = [
                0 => [
                    "journeyDuration" => $info['options'][0]['ArrivalDateTimeGo'],
                    "numberOfStop" => 0,
                    "flightSegments" =>  [
                        0 => [
                            "flightNumber" => $info['options'][0]['FlightNumberGo'],
                            "departureDateTime" => $info['options'][0]['DepartureDateTimeGo'],
                            "arrivalDateTime" => $info['options'][0]['ArrivalDateTimeGo'],
                            "resBookDesigCode" => "Y",
                            "arrivalAirport" => [
                                "locationCode" => $info['options'][0]['ArivelLocationCodeGo'],
                                "terminalID" => null,
                                "locationName" => $info['options'][0]['destinationAirportGo'],
                                "countryName" => $info['options'][0]['destinationCountryGo'] ?? null,
                                "cityName" => $info['options'][0]['destinationCityGo'],
                            ],
                            "departureAirport" =>  [
                                "locationCode" => $info['options'][0]['DeparturLocationCodeGo'],
                                "terminalID" => null,
                                "locationName" => $info['options'][0]['originAirportGo'],
                                "countryName" => $info['options'][0]['originCountryGo'] ?? null,
                                "cityName" => $info['options'][0]['originCityGo'] ?? null,
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
                    "journeyDuration" => $info['options'][0]['FlightDurationBack'],
                    "numberOfStop" => 0,
                    "flightSegments" =>  [
                        0 => [
                            "flightNumber" => $info['options'][0]['FlightNumberBack'],
                            "departureDateTime" => $info['options'][0]['DepartureDateTimeBack'],
                            "arrivalDateTime" => $info['options'][0]['ArrivalDateTimeBack'],
                            "resBookDesigCode" => "Y",
                            "arrivalAirport" => [
                                "locationCode" => $info['options'][0]['ArivelLocationCodeBack'],
                                "terminalID" => null,
                                "locationName" => $info['options'][0]['destinationAirportBack'],
                                "countryName" => $info['options'][0]['destinationCountryBack'] ?? null,
                                "cityName" => $info['options'][0]['destinationCityBack'],
                            ],
                            "departureAirport" =>  [
                                "locationCode" => $info['options'][0]['DeparturLocationCodeBack'],
                                "terminalID" => null,
                                "locationName" => $info['options'][0]['originAirportBack'],
                                "countryName" => $info['options'][0]['originCountryBack'] ?? null,
                                "cityName" => $info['options'][0]['originCityBack'] ?? null,
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
            $expiration = Carbon::parse($info['options'][0]['DepartureDateTime'])->subDay(1)->format('Y-m-d H:i');
            $originDestinationOptions = [
                "journeyDuration" => $info['options'][0]['ArrivalDateTime'],
                "numberOfStop" => 0,
                "flightSegments" =>  [
                    0 => [
                        "flightNumber" => $info['options'][0]['FlightNumber'],
                        "departureDateTime" => $info['options'][0]['DepartureDateTime'],
                        "arrivalDateTime" => $info['options'][0]['ArrivalDateTime'],
                        "resBookDesigCode" => "Y",
                        "arrivalAirport" => [
                            "locationCode" => $info['options'][0]['ArivelLocationCode'],
                            "terminalID" => null,
                            "locationName" => $info['options'][0]['destinationAirport'],
                            "countryName" => $info['options'][0]['destinationCountry'] ?? null,
                            "cityName" => $info['options'][0]['destinationCity'],
                        ],
                        "departureAirport" =>  [
                            "locationCode" => $info['options'][0]['DeparturLocationCode'],
                            "terminalID" => null,
                            "locationName" => $info['options'][0]['originAirport'],
                            "countryName" => $info['options'][0]['originCountry'] ?? null,
                            "cityName" => $info['options'][0]['originCity'] ?? null,
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
        $reserver = [
            "firstName" => "demo",
            "lastName" => "demo",
            "email" => "demo@matin.com",
            "phoneNumber" => "9354553465",
            "username" => "demo@matin.com",
            "faxNumber" => null,
            "telNumber" => null,
            "gender" => true,
            "countryCallingCode" => null
        ];
        // dd($originDestinationOptions);
        $reserved = new NewItoursReserve();
        $reserved->pnr = $pnr;
        $reserved->key_validate = $key;
        $reserved->reserver = $reserver;
        $reserved->flight_detail =  $originDestinationOptions;
        $reserved->passengers_info = $passengers;
        $reserved->reserved_id = $reservId;
        $reserved->expiration_time = $expiration;
        $reserved->trip_type = $validate->flightType;
        $reserved->passengers_baseFare = $basefare;
        $reserved->pricing = $info['pricing'];
        $reserved->booked_dateTime = Carbon::now();
        $reserved->extra_baggage = $hasExtraBaggage;
        $reserved->save();

        return response(
            [
                "success" => true,
                "result" => [
                    "reserveStatus" => "Pending",
                    "pnrCode" => "$pnr",
                    "bookedDateTime" => Carbon::now(),
                    "airItinerary" => [
                        "originDestinationOptions" => 
                            $originDestinationOptions
                    ],
                    "passengers" =>  $passengers,
                    "reserver" =>  $reserver,
                    "providerName" => "Sabre",
                    "passengersFare" => [
                        $basefare,
                        "fare" => [
                            "baseFare" => $info['pricing']['totalbasefare'],
                            "totalTaxes" =>  $info['pricing']['taxFare'],
                            "totalFare" =>  $info['pricing']['totalTotalefare'],
                            "totalFee" => 0.0,
                            "extraBaggage" => 0.0,
                            "totalFareWithExtraBaggage" => 0.0,
                        ]
                    ],

                    "isDomestic" => false,
                    "airTripType" => $validate->flightType,
                    "hasExtraBaggage" => $hasExtraBaggage,
                    "reserveId" => $reservId,
                    "expirationTime" => $expiration,
                    "flightType" => "Systemic",
                    "priceChangedAtReserve" => false,
                    "hasOnlineVoid" => false
                ],
                "error" => null,

            ],
            200
        );
    }

    public function getReserveDetail()
    {
        extract(request()->all());
        $header = request()->header();
        $token = NewItoursAuthe::where('accessToken', substr($header['authorization'][0], 7))->where('expireInSeconds', '>', Carbon::now())->first();
        // if (!isset($token)) {
        //     return response([], 401);
        // }
        $reserved = NewItoursReserve::where('reserved_id', $reserveId)->first();
        if (empty($reserved)) {
            return response([
                "success" => false,
                "result" => null,
                "error" => [
                    "code" => 5010,
                    "message" => "Unknown error has occurred"
                ]
            ]);
        }
        // return response()->json(json_decode(view("Itours.NewItours.Reserve.reserveSuccess", ['result' => $reserved])->render(), true));

        return response(
            [
                "success" => true,
                "result" => [
                    "reserveStatus" => "Pending",
                    "pnrCode" => $reserved->pnr,
                    "bookedDateTime" => $reserved->booked_dateTime,
                    "airItinerary" => [
                        "originDestinationOptions" =>
                            $reserved->flight_detail
                    ],
                    "passengers" =>  $reserved->passengers_info,
                    "reserver" =>  $reserved->reserver,
                    "providerName" => "Sabre",
                    "passengersFare" => [
                        $reserved->passengers_baseFare,
                        "fare" => [
                            "baseFare" => $reserved->pricing['totalbasefare'],
                            "totalTaxes" =>  $reserved->pricing['taxFare'],
                            "totalFare" =>  $reserved->pricing['totalTotalefare'],
                            "totalFee" => 0.0,
                            "extraBaggage" => 0.0,
                            "totalFareWithExtraBaggage" => 0.0,
                        ]
                    ],

                    "isDomestic" => false,
                    "airTripType" => $reserved->trip_type,
                    "hasExtraBaggage" => $reserved->extra_baggage ?? false,
                    "reserveId" => $reserved->reserved_id,
                    "expirationTime" => $reserved->expiration_time,
                    "flightType" => "Systemic",
                    "priceChangedAtReserve" => false,
                    "hasOnlineVoid" => false
                ],
                "error" => null,

            ],
            200
        );
    }

    public function confirmation()
    {
        extract(request()->all());
        $header = request()->header();
        $token = NewItoursAuthe::where('accessToken', substr($header['authorization'][0], 7))->where('expireInSeconds', '>', Carbon::now())->first();
        if (!isset($token)) {
            return response([], 401);
        }

        $reserved = NewItoursReserve::where('reserved_id', $reserveId)->first();
        if (empty($reserved)) {
            return response([
                "success" => false,
                "result" => null,
                "error" => [
                    "code" => 5010,
                    "message" => "Unknown error has occurred"
                ]
            ]);
        }
        if ($reserved->expiration_time > Carbon::now()) {
            $status = 'Issued';
        } else {
            $status = 'Canceled';
        }

        return response(
            [
                "success" => true,
                "result" => [
                    "reserveStatus" => "$status",
                    "pnrCode" => $reserved->pnr,
                    "bookedDateTime" => $reserved->booked_dateTime,
                    "airItinerary" => [
                        "originDestinationOptions" =>  
                            $reserved->flight_detail
                    ],
                    "passengers" =>  $reserved->passengers_info,
                    "reserver" =>  $reserved->reserver,
                    "providerName" => "Sabre",
                    "passengersFare" => [
                        $reserved->passengers_baseFare,
                        "fare" => [
                            "baseFare" => $reserved->pricing['totalbasefare'],
                            "totalTaxes" =>  $reserved->pricing['taxFare'],
                            "totalFare" =>  $reserved->pricing['totalTotalefare'],
                            "totalFee" => 0.0,
                            "extraBaggage" => 0.0,
                            "totalFareWithExtraBaggage" => 0.0,
                        ]
                    ],

                    "isDomestic" => false,
                    "airTripType" => $reserved->trip_type,
                    "hasExtraBaggage" => $reserved->extra_baggage ?? false,
                    "reserveId" => $reserved->reserved_id,
                    "expirationTime" => $reserved->expiration_time,
                    "flightType" => "Systemic",
                    "priceChangedAtReserve" => false,
                    "hasOnlineVoid" => false
                ],
                "error" => null,

            ],
            200
        );
    }
}
