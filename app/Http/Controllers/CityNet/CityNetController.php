<?php

namespace App\Http\Controllers\CityNet;

use App\Http\Controllers\Controller;
use App\Models\CityNetFlightsBooking;
use App\Models\CityNetFlightsRule;
use App\Models\CityNetSearch;
use App\Models\CityNetSelectedFlight;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CityNetController extends Controller
{
    public $Recaptcha = ['a2yu5', '2xp78', 'rth5n', '56lsz', 'hve14'];

    public function Login()
    {
        function base64url_encode($str)
        {
            return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
        }

        ///> generate jwt token
        function generate_jwt($headers, $payload, $secret = 'secret')
        {
            $headers_encoded = base64url_encode(json_encode($headers));

            $payload_encoded = base64url_encode(json_encode($payload));

            $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
            $signature_encoded = base64url_encode($signature);

            $jwt = "$headers_encoded.$payload_encoded.$signature_encoded";

            return $jwt;
        }

        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            "username" => ["required", Rule::in(['matinintco@gmail.com'])],
            "password" => ["required", Rule::in(['matin3670'])],
        ]);

        ///> validation fails
        if ($validated->fails()) {
            return response([
                'Success' => false,
                'Error' => [
                    "Message" => "Invalid login credentials supplied",
                ],
                "token" => null,
            ],422);
        }

        ///> creat a jwt token - 1-header , 2-payload , 3-signature

        ///> 1- header
        $headers = [
            "alg" => "HS256",
            "typ" => "JWT"
        ];

        ///> 2-payload
        $payload = [
            "orgId" => 10,
            "accountId" => 42752,
            "origin" => "https://www.demo.citynet.ir",
            "iat" => Carbon::now()->timestamp,
            "exp" => Carbon::now()->addMinute(60)->timestamp,
            "aud" => "https://citysoft.ir",
            "iss" => "Citynet corp",
            "sub" => "support@citynet.ir"
        ];

        $token = generate_jwt($headers, $payload);

        ///> store in db
        $session = new Session();
        $session->sessionId = $token;
        $session->seller = 'citynet';
        $session->expired_at = Carbon::now()->addMinute(60);
        $session->save();

        return response([
            "Success" => true,
            "token" => $token
        ]);
    }

    public function Search()
    {
        extract(request()->all());
        // return request()->all();

        ///> request validation
        $validated = Validator::make(request()->all(), [
            "Lang" => "required",
            "TravelPreference" => "required",
            "TravelerInfoSummary" => "required",
            "TravelerInfoSummary.AirTravelerAvail.PassengerTypeQuantity" => "required",
            "OriginDestinationInformations" => "required",
            "OriginDestinationInformations.*.OriginLocation" => "required",
            "OriginDestinationInformations.*.DestinationLocation" => "required",
        ], [
            "Lang.required" => "Lang,body,Language is require,any.required",
            "TravelPreference.required" => "TravelPreference,body,TravelPreference is required,any.required",
            "TravelerInfoSummary.required" => "TravelerInfoSummary,body,TravelerInfoSummary is require,any.required",
            "TravelerInfoSummary.AirTravelerAvail.PassengerTypeQuantity.required" => "PassengerTypeQuantity,body,There should be at least one adult in flight,any.required",
            "OriginDestinationInformations.required" => "OriginDestinationInformations,body,OriginDestinationInformations is require,any.required",
            "OriginDestinationInformations.*.OriginLocation.required" => "OriginDestinationInformations.OriginLocation,body,OriginLocation is require,any.required",
            "OriginDestinationInformations.*.DestinationLocation.required" => "OriginDestinationInformations.DestinationLocation,body,DestinationLocation is require,any.required",
        ]);

        ///> validation fails
        if ($validated->fails()) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 422,
                "Items" => [
                    "Field" => explode(",", $validated->messages()->first())[0],
                    "Location" => explode(",", $validated->messages()->first())[1],
                    "messages" => explode(",", $validated->messages()->first())[2],
                    "type" => explode(",", $validated->messages()->first())[3]
                ],
            ], 422);
        }

        
        ///> check token
        if (empty(request()->header()['authorization'])) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 422,
                "Items" => [
                    "Field" => "token",
                    "Location" => "header",
                    "messages" => "Invalid token",
                    "type" => "NotValid"
                ],
            ], 422);
        }
        $token = explode(' ', request()->header()['authorization'][0])[1];
        $CheckToken = Session::where('sessionId', $token)->first();
        if (is_null($CheckToken) || $CheckToken->expired_at < Carbon::now()) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 422,
                "Items" => [
                    "Field" => "token",
                    "Location" => "header",
                    "messages" => "Invalid token",
                    "type" => "NotValid"
                ],
            ], 422);
        }
        
        ///> Cabin Type
        $Cabin = $TravelPreference['CabinPref']['Cabin'];

        ///> Passengers Count
        $AdultCount = 0;
        $ChildCount = 0;
        $InfantCount = 0;
        $CheckPassengers = $TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity'];
        foreach ($CheckPassengers as $passenger) {
            if ($passenger['Code'] == "ADT") {
                $AdultCount += $passenger['Quantity'];
            } else if ($passenger['Code'] == "CHD") {
                $ChildCount += $passenger['Quantity'];
            } else if ($passenger['Code'] == "INF") {
                $InfantCount += $passenger['Quantity'];
            }
        }
        if ($AdultCount == 0) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 422,
                "Items" => [
                    "Field" => "PassengerTypeQuantity",
                    "Location" => "body",
                    "messages" => "There should be at least one adult in flight",
                    "type" => "any.required"
                ],
            ], 422);
        }

        ///> Origin-Destination infos
        $OriginCode = $OriginDestinationInformations[0]['OriginLocation']['LocationCode'];
        $DestinationCode = $OriginDestinationInformations[0]['DestinationLocation']['LocationCode'];
        $DepartureDateTime = $OriginDestinationInformations[0]['DepartureDateTime'];

        ///> search for flights
        $CheckFlights = CityNetSearch::where('AdultCount', $AdultCount)->where('ChildCount', $ChildCount)->where('InfantCount', $InfantCount)->where('OriginCode', $OriginCode)->where('DestinationCode', $DestinationCode)->where('DepartureDateTime', 'LIKE', "%$DepartureDateTime%")->first();
        if (is_null($CheckFlights)) {
            return response([
                'Success' => false,
                'Error' => [
                    "Message" => "No flight(s) found",
                ],
                "Items" => []
            ]);
        }

        ///> store selected flight
        $selected = CityNetSelectedFlight::where('SessionId', $CheckToken->id)->where('FlightId', $CheckFlights->id)->first();
        if (is_null($selected)) {
            $selected = new CityNetSelectedFlight();
            $selected->SessionId = $CheckToken->id;
            $selected->FlightId = $CheckFlights->id;
            $selected->save();
        }

        return response($CheckFlights->response, 200)->header('Content-Type','application/json');
    }

    public function Rules()
    {
        extract(request()->all());
        // return request()->all();

        ///> request validation
        $validated = Validator::make(request()->all(), [
            'PassengerType' => ['required', Rule::in(['ADT', 'CHD', 'INF'])],
            'AirItinerary' => 'required',
            'AirItinerary.*.SessionId' => 'required',
        ], [
            'PassengerType.required' => 'Passenger Type is required',
            'AirItinerary.required' => 'Flight informations is required',
            'AirItinerary.*.SessionId.required' => 'Flight informations is required'
        ]);

        if ($validated->fails()) {
            return response([
                'Success' => false,
                'Error' => [
                    "Message" => $validated->messages()->first(),
                ],
            ]);
        }

        ///> check token
        if (empty(request()->header()['authorization'])) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "token",
                    "Location" => "header",
                    "messages" => "Invalid token",
                    "type" => "NotValid"
                ],
            ], 400);
        }
        $token = explode(' ', request()->header()['authorization'][0])[1];
        $CheckToken = Session::where('sessionId', $token)->first();
        if (is_null($CheckToken) || $CheckToken->expired_at < Carbon::now()) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "token",
                    "Location" => "header",
                    "messages" => "Invalid token",
                    "type" => "NotValid"
                ],
            ], 400);
        }

        ///> check flight session id
        $FlightSessionId = $AirItinerary[0]['SessionId'];
        $FlightCombinationId = $AirItinerary[0]['CombinationId'];
        $FlightRecommendationId = $AirItinerary[0]['RecommendationId'];
        $FlightSubsystemId = $AirItinerary[0]['SubsystemId'];
        $FlightSubsystemName = $AirItinerary[0]['SubsystemName'];
        $CheckFlightSessionId = CityNetSearch::where('response', 'LIKE', "%$FlightSessionId%")->first();
        if (is_null($CheckFlightSessionId)) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "AirTraveler",
                    "Location" => "body",
                    "messages" => "Flight not found",
                    "type" => "NotValid"
                ],
            ]);
        }

        ///> find flight info from it's session id
        foreach ($CheckFlightSessionId['response']['Items'] as $item) {
            if (
                ($FlightSessionId == $item['AirItinerary'][0]['SessionId'])
                && ($FlightCombinationId == $item['AirItinerary'][0]['CombinationId'])
                && ($FlightRecommendationId == $item['AirItinerary'][0]['RecommendationId'])
                && ($FlightSubsystemId == $item['AirItinerary'][0]['SubsystemId'])
                && ($FlightSubsystemName == $item['AirItinerary'][0]['SubsystemName'])
            ) {
                $AirLine = $item['OriginDestinationInformation']['OriginDestinationOption'][0]['FlightSegment'][0]['MarketingAirline']['Code'];
                $DepartureCode = $item['OriginDestinationInformation']['OriginDestinationOption'][0]['OriginLocation'];
                $ArrivalCode = $item['OriginDestinationInformation']['OriginDestinationOption'][0]['DestinationLocation'];
            }
        }

        ///> find rule
        $findRule = CityNetFlightsRule::where('DepartureLocationCode', $DepartureCode)->where('ArrivalLocationCode', $ArrivalCode)->where('AirLine', $AirLine)->where('PassengerType', $PassengerType)->first();
        if (is_null($findRule)) {
            return response([
                "Items" => [
                    "Airline" => "",
                    "DetailRules" => [
                        "Text" => [
                            "جهت اطلاع از قوانین ومقررات استرداد این پرواز با پشتیبانی تماس بگیرید"
                        ],
                        "Subtitle" => "Penalties "
                    ],
                    "MarketAirline" => "",
                    "ArrivalLocationCode" => "",
                    "DepartureLocationCode" => ""
                ],
                "Success" => true
            ]);
        }

        return response($findRule['response']);
    }

    public function Book()
    {
        extract(request()->all());
        // return request()->header();

        ///> request validation
        $validated = Validator::make(request()->all(), [
            'Transports.AirItinerary' => 'required',
            'Transports.AirItinerary.*.SessionId' => 'required',
            'Transports.AirItinerary.*.SubsystemName' => 'required',
            'Transports.TravelerInfo' => 'required',
            'Transports.TravelerInfo.AirTraveler' => 'required',
            'currencyToPay' => ['required', Rule::in(['IRR'])],
        ], [
            'Transports.AirItinerary.required' => 'AirItinerary is required',
            'Transports.AirItinerary.*.SessionId.required' => 'Invalid flight informations',
            'Transports.AirItinerary.*.SubsystemName.required' => 'Invalid flight informations',
            'Transports.TravelerInfo.required' => 'Please enter Traveler(s) informations',
            'Transports.TravelerInfo.AirTraveler.required' => '',
            'currencyToPay.required' => 'Please enter your currency to pay',
        ]);

        ///> validation fails
        if ($validated->fails()) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "Transports",
                    "Location" => "body",
                    "messages" => $validated->messages()->first(),
                    "type" => "NotValid"
                ],
            ], 400);
        }

        ///> check token
        if (empty(request()->header()['authorization'])) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "token",
                    "Location" => "header",
                    "messages" => "Invalid token",
                    "type" => "NotValid"
                ],
            ], 400);
        }
        $token = explode(' ', request()->header()['authorization'][0])[1];
        $CheckToken = Session::where('sessionId', $token)->first();
        if (is_null($CheckToken) || $CheckToken->expired_at < Carbon::now()) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "token",
                    "Location" => "header",
                    "messages" => "Invalid token",
                    "type" => "NotValid"
                ],
            ], 400);
        }

        ///> get flight informations
        $FindSelectedFlight = CityNetSelectedFlight::where('SessionId', $CheckToken->id)->orderByDesc('id')->first();
        if (is_null($FindSelectedFlight)) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "AirItinerary",
                    "Location" => "body",
                    "messages" => "Please select a flight",
                    "type" => "NotValid"
                ],
            ], 400);
        }

        $SelectedFlightInfo = CityNetSearch::where('id', $FindSelectedFlight->FlightId)->first();
        if (is_null($SelectedFlightInfo)) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "AirItinerary",
                    "Location" => "body",
                    "messages" => "Flight not found",
                    "type" => "NotValid"
                ],
            ], 400);
        }
        $SelectedFlightResponse = $SelectedFlightInfo['response'];
        ///> check flight info in request and in db
        $SessionIdRequest = $Transports['AirItinerary'][0]['SessionId'];
        $CombinationIdRequest = $Transports['AirItinerary'][0]['CombinationId'];
        $RecommendationIdRequest = $Transports['AirItinerary'][0]['RecommendationId'];
        $SubsystemIdRequest = $Transports['AirItinerary'][0]['SubsystemId'];
        $SubsystemNameRequest = $Transports['AirItinerary'][0]['SubsystemName'];

        $FlightAirItinerary = [];
        $FlightId = '';
        $FlightPriceInfo = [];

        foreach ($SelectedFlightResponse['Items'] as $item) {
            if (($item['AirItinerary'][0]['SessionId'] == $SessionIdRequest) && ($item['AirItinerary'][0]['SubsystemId'] == $SubsystemIdRequest) && ($item['AirItinerary'][0]['CombinationId'] == $CombinationIdRequest) && ($item['AirItinerary'][0]['SubsystemName'] == $SubsystemNameRequest) && ($item['AirItinerary'][0]['RecommendationId'] == $RecommendationIdRequest)) {

                ///> if it's subsystemName = charter724 => check recaptcha
                if ($SubsystemNameRequest == "charter724") {
                    if (!isset($Transports['Captcha']) || empty($Transports['Captcha']) || !in_array($Transports['Captcha'], $this->Recaptcha)) {
                        return response([
                            "Name" => "ValidationError",
                            "Success" => false,
                            "Status" => 400,
                            "Items" => [
                                "Field" => "AirItinerary",
                                "Location" => "body",
                                "messages" => "Invalid Recaptcha",
                                "type" => "NotValid"
                            ],
                        ], 400);
                    }
                }

                $FlightAirItinerary = [
                    "SessionId" => $SessionIdRequest,
                    "SubsystemId" => $SubsystemIdRequest,
                    "CombinationId" => $CombinationIdRequest,
                    "SubsystemName" => $SubsystemNameRequest,
                    "RecommendationId" => $RecommendationIdRequest,
                ];
                $FlightId = $SelectedFlightInfo['id'];
                $FlightPriceInfo = $item['AirItineraryPricingInfo'];
            }
        }

        if (empty($FlightId) || empty($FlightAirItinerary) || empty($FlightPriceInfo)) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "AirItinerary",
                    "Location" => "body",
                    "messages" => "Flight not found",
                    "type" => "NotValid"
                ],
            ], 400);
        }

        ///> check traveler
        $AdultCount = $SelectedFlightInfo['AdultCount'];
        $ChildCount = $SelectedFlightInfo['ChildCount'];
        $InfantCount = $SelectedFlightInfo['InfantCount'];
        $totalPassengersCount = $AdultCount + $ChildCount + $InfantCount;
        if ($totalPassengersCount != count($Transports['TravelerInfo']['AirTraveler'])) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "AirTraveler",
                    "Location" => "body",
                    "messages" => "Invalid traveler(s) information(s)",
                    "type" => "NotValid"
                ],
            ], 400);
        }

        ///> check duplicate booking
        $AlreadyBooked = CityNetFlightsBooking::where('SessionId', $CheckToken->id)->where('FlightId', $FlightId)->first();
        if (!is_null($AlreadyBooked)) {
            $traveler = json_decode($AlreadyBooked['TravelerInfo'], true);
            if (count($Transports['TravelerInfo']['AirTraveler']) == count($traveler['AirTraveler'])) {
                foreach ($Transports['TravelerInfo']['AirTraveler'] as $key => $checkTraveler) {
                    if (
                        ($checkTraveler['PersonName']['GivenName'] == $traveler['AirTraveler'][$key]['PersonName']['GivenName'])
                        && ($checkTraveler['PersonName']['Surname'] == $traveler['AirTraveler'][$key]['PersonName']['Surname'])
                        && ($checkTraveler['Document']['DocID'] == $traveler['AirTraveler'][$key]['Document']['DocID'])
                        && ($checkTraveler['NationalId'] == $traveler['AirTraveler'][$key]['NationalId'])
                        && ($checkTraveler['PassengerTypeCode'] == $traveler['AirTraveler'][$key]['PassengerTypeCode'])
                    ) {
                        return response([
                            "Name" => "ValidationError",
                            "Success" => false,
                            "Status" => 400,
                            "Items" => [
                                "Field" => "AirTraveler",
                                "Location" => "body",
                                "messages" => "Flight Already booked",
                                "type" => "NotValid"
                            ],
                        ], 400);
                    }
                }
            }
        }

        ///> store booking
        ///> create ticket number per passenger
        $new = [];
        foreach ($Transports['TravelerInfo']['AirTraveler'] as $key => $passenger) {
            $new[] = $passenger;
            $new[$key]['PersonId'] = rand(10, 999);
            $new[$key]['PassengerId'] = rand(100, 99999);
            $new[$key]['TicketNumber'] = [hexdec(uniqid())];
        }
        $TravelerInfo = [
            "AirTraveler" => $new,
        ];

        $pnr = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 5);
        $booking = new CityNetFlightsBooking();
        $booking->SessionId = $CheckToken->id;
        $booking->FlightId = $FlightId;
        $booking->FlightAirItinerary = json_encode($FlightAirItinerary);
        $booking->FlightPriceInfo = json_encode($FlightPriceInfo);
        $booking->TravelerInfo = json_encode($TravelerInfo);
        $BookId = uniqid();
        $booking->BookId = $BookId;
        $ContractNo = rand(100, 100000);
        $booking->pnr = $pnr;
        $booking->ContractNo = $ContractNo;
        $booking->Currency = $currencyToPay;
        $booking->TicketTimeLimit = Carbon::now();
        $booking->save();

        return response([
            'BookId' => $BookId,
            "ContractInfo" => [
                "ContractNo" => $ContractNo,
                "Amount" => $FlightPriceInfo['ItinTotalFare']['TotalFare'],
                "Currency" => $currencyToPay,
            ],
            "Items" => [
                "Success" => true,
                "Items" => [
                    "AirReservation" => [
                        "BookingReferenceID" => [
                            "TicketTimeLimit" => Carbon::now(),
                            "TicketType" => "LockingOnly",
                        ],
                        "Ticketing" => [
                            "type" => "Lock",
                            "ID_Context" => $pnr
                        ],
                        "AirItineraryPricingInfo" => $FlightPriceInfo,
                    ],
                ],
                "AirItinerary" => $FlightAirItinerary,
            ],
        ]);
    }

    public function Ticket($contractNo)
    {
        ///> check contract no in url
        if (empty($contractNo)) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "contractNo",
                    "Location" => "body",
                    "messages" => "Invalid contractNo",
                    "type" => "NotValid"
                ],
            ], 400);
        }

        ///> check token
        if (empty(request()->header()['authorization'])) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "token",
                    "Location" => "header",
                    "messages" => "Invalid token",
                    "type" => "NotValid"
                ],
            ], 400);
        }
        $token = explode(' ', request()->header()['authorization'][0])[1];
        $CheckToken = Session::where('sessionId', $token)->first();
        if (is_null($CheckToken) || $CheckToken->expired_at < Carbon::now()) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "token",
                    "Location" => "header",
                    "messages" => "Invalid token",
                    "type" => "NotValid"
                ],
            ], 400);
        }

        ///> search ticket
        $findTicket = CityNetFlightsBooking::where('ContractNo', $contractNo)->first();
        if (is_null($findTicket)) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "contractNo",
                    "Location" => "body",
                    "messages" => "Invalid contractNo",
                    "type" => "NotValid"
                ],
            ], 400);
        }

        ///> tickt published or not
        if (!empty($findTicket['Ticket'])) {
            return response(json_decode($findTicket['Ticket'], true));
        }

        $FlightAirItinerary = json_decode($findTicket['FlightAirItinerary'], true);
        $TravelerInfo = json_decode($findTicket['TravelerInfo'], true);

        $TicketResponse = [
            "Items" => [
                "Success" => true,
                "Items" => [
                    "AirItinerary" => [
                        "SessionId" => $FlightAirItinerary['SessionId'],
                        "CombinationId" => $FlightAirItinerary['CombinationId'],
                        "RecommendationId" => $FlightAirItinerary['RecommendationId'],
                        "SubsystemId" => $FlightAirItinerary['SubsystemId'],
                        "SubsystemName" => $FlightAirItinerary['SubsystemName'],
                        "pnr" => $findTicket['pnr'],
                        "Eticket" => $TravelerInfo['AirTraveler'][0]['TicketNumber'][0]
                    ],
                    "TravelerInfo" => $TravelerInfo
                ]
            ]
        ];

        CityNetFlightsBooking::where('ContractNo', $contractNo)->update(['Ticket' => json_encode($TicketResponse)]);

        return response($TicketResponse, 200);
    }

    public function SingleReport()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            'SessionId' => 'required',
            'CombinationId' => 'required',
            'SubsystemId' => 'required',
            'ContractNo' => 'required',
        ], [
            'SessionId.required' => 'SessionId,SessionId is required',
            'CombinationId.required' => 'CombinationId,CombinationId is required',
            'SubsystemId.required' => 'SubsystemId,SubsystemId is required',
            'ContractNo.required' => 'ContractNo,ContractNo is required',
        ]);

        ///> if validation fails
        if ($validated->fails()) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => explode(",", $validated->messages()->first())[0],
                    "Location" => "body",
                    "messages" => explode(",", $validated->messages()->first())[1],
                    "type" => "NotValid"
                ],
            ], 400);
        }

        ///> check token
        if (empty(request()->header()['authorization'])) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "token",
                    "Location" => "header",
                    "messages" => "Invalid token",
                    "type" => "NotValid"
                ],
            ], 400);
        }
        $token = explode(' ', request()->header()['authorization'][0])[1];
        $CheckToken = Session::where('sessionId', $token)->first();
        if (is_null($CheckToken) || $CheckToken->expired_at < Carbon::now()) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "token",
                    "Location" => "header",
                    "messages" => "Invalid token",
                    "type" => "NotValid"
                ],
            ], 400);
        }

        ///> find book
        $findBook = CityNetFlightsBooking::where('ContractNo', $ContractNo)->first();
        if (is_null($findBook)) {
            return response([
                "Name" => "ValidationError",
                "Success" => false,
                "Status" => 400,
                "Items" => [
                    "Field" => "",
                    "Location" => "body",
                    "messages" => "Invalid book informations",
                    "type" => "NotValid"
                ],
            ], 400);
        }

        ///> report already published or not
        if (!empty($findBook['Report'])) {
            return response(json_decode($findBook['Report'], true));
        }

        ///> not published
        $TravelerInfo = json_decode($findBook['TravelerInfo'], true);
        $info = [];
        foreach ($TravelerInfo['AirTraveler'] as $key => $Traveler) {
            if ($Traveler['PersonName']['NamePrefix'] == 'MR' || $Traveler['PersonName']['NamePrefix'] == 'Mr' || $Traveler['PersonName']['NamePrefix'] == 'mr') {
                $Gender = "Male";
            } else {
                $Gender = "Female";
            }
            $info[$key]['Id'] = $Traveler['PersonId'];
            $info[$key]['Email'] = $Traveler['Email'];
            $info[$key]['Gender'] = $Gender;
            $info[$key]['Mobile'] = isset($Traveler['Mobile']) ? $Traveler['Mobile'] : "";
            $info[$key]['Comment'] = "";
            $info[$key]['Eticket'] = $Traveler['TicketNumber'][0];
            $info[$key]['SurName'] = $Traveler['PersonName']['Surname'];
            $info[$key]['BirthDate'] = $Traveler['BirthDate'];
            $info[$key]['GivenName'] = $Traveler['PersonName']['GivenName'];
            $info[$key]['MobileCode'] = "";
            $info[$key]['NamePrefix'] = $Traveler['PersonName']['NamePrefix'];
            $info[$key]['NationalId'] = $Traveler['NationalId'];
            $info[$key]['PassportNO'] = $Traveler['NationalId'];
            $info[$key]['Nationality'] = "IQ";
            $info[$key]['PassengerId'] = $Traveler['PassengerId'];
            $info[$key]['ReferenceId'] = $findBook['pnr'];
            $info[$key]['InnerDocType'] = $Traveler['Document']['InnerDocType'];
            $info[$key]['PassengerTypeCode'] = $Traveler['PassengerTypeCode'];
            $info[$key]['PassportExpireDate'] = $Traveler['Document']['ExpireDate'];
        }

        $ReportResponse = [
            "Success" => true,
            "Items" => [
                "serviceId" => 1,
                "Passengers" => $info,
                "Rooms" => null
            ]
        ];

        CityNetFlightsBooking::where('ContractNo', $ContractNo)->update(['Report' => json_encode($ReportResponse)]);

        return response($ReportResponse, 200);
    }
}
