<?php

namespace App\Http\Controllers\Parto;

use App\Http\Controllers\Controller;
use App\Models\PartoBooking;
use App\Models\PartoBookingData;
use App\Models\PartoFlightSearch;
use App\Models\PartoSelectedFlight;
use App\Models\Session;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Air extends Controller
{
    public function lowFareSearch()
    {
        extract(request()->all());

        ///> validation request
        $validated = Validator::make(request()->all(), [
            'SessionId' => 'required',
            'AdultCount' => 'required|min:1|numeric',
            'OriginDestinationInformations' => 'required|array',
            'OriginDestinationInformations.*.OriginLocationCode' => 'required|string',
            'OriginDestinationInformations.*.DepartureDateTime' => 'required|after:now',
            'OriginDestinationInformations.*.DestinationLocationCode' => 'required|string',
            'TravelPreference.CabinType' => ["required", "numeric", Rule::in([1, 2, 3, 4, 5, 6, 100])],
            'TravelPreference.AirTripType' => ["required", "numeric", Rule::in([1, 2])],
        ], [
            'SessionId.required' => 'SessionID cannot be null,Err0102007',
            'AdultCount.required' => 'There should be atleast one Adult,Err0103014',
            'AdultCount.min' => 'There should be atleast one Adult,Err0103014',
            'OriginDestinationInformations.required' => 'OriginDestinationInformation cannot be null,Err0103004',
            'OriginDestinationInformations.array' => 'OriginDestinationInformation cannot be null,Err0103004',
            'OriginDestinationInformations.OriginLocationCode.required' => 'OriginLocationCode cannot be null,Err0103005',
            'OriginDestinationInformations.OriginLocationCode.string' => 'OriginLocationCode cannot be null,Err0103005',
            'OriginDestinationInformations.*.DepartureDateTime.required' => 'Travel date should be more than 24 hours before departure,Err0103011',
            'OriginDestinationInformations.*.DepartureDateTime.after' => 'Travel date should be more than 24 hours before departure,Err010301',
            'OriginDestinationInformations.DestinationLocationCode.required' => 'DestinationLocationCode cannot be null,Err0103006',
            'OriginDestinationInformations.DestinationLocationCode.string' => 'DestinationLocationCode cannot be null,Err0103006',
            'TravelPreference.CabinType.required' => 'Invalid CabinType,Err0103019',
            'TravelPreference.CabinType.numeric' => 'Invalid CabinType,Err0103019',
            'TravelPreference.CabinType.in' => 'Invalid CabinType,Err0103019',
            'TravelPreference.AirTripType.required' => 'Invalid AirTripType,Err0103020',
            'TravelPreference.AirTripType.numeric' => 'Invalid AirTripType,Err0103020',
            'TravelPreference.AirTripType.in' => 'Invalid AirTripType,Err0103020',
        ]);

        ///> if validation fails
        if ($validated->fails()) {
            return response([
                "Error" => [
                    "Id" => explode(',', $validated->messages()->first())[1],
                    "Message" => explode(',', $validated->messages()->first())[0],
                ],
                "Success" => false,
                "SessionId" => null,
            ], 422);
        }

        ///> check session
        $session = Session::where('sessionId', $SessionId)->first();
        if (isset($session) && $session->expired_at < Carbon::now()) {
            return response([
                "Error" => [
                    "Id" => "Err0102008",
                    "Message" => "Invalid SessionID"
                ],
                "Success" => false,
                "TktTimeLimit" => null,
                "Category" => null,
                "Status" => null,
                "UniqueId" => null,
                "PriceChange" => false
            ], 422);
        }

        ///> check trip type - TripType = 1      oneWay(1 destination)
        if ($TravelPreference['AirTripType'] == 1 && count($OriginDestinationInformations) == 2) {
            return response([
                "Error" => [
                    "Id" => "Err0103003",
                    "Message" => "Invalid AirTripType. OneWay cannot have more than one OriginDestinationInformation"
                ],
                "Success" => false,
                "PricedItineraries" => [],
            ], 422);
        }

        ///> check trip type - TripType = 2       more than 1 destination
        if ($TravelPreference['AirTripType'] == 2 && count($OriginDestinationInformations) == 1) {
            return response([
                "Error" => [
                    "Id" => "Err0101003",
                    "Message" => "An unexpected error occurred while processing the AirLowFareSearch call. API Support notified!"
                ],
                "Success" => false,
                "PricedItineraries" => [],
            ], 422);
        }

        if (
            isset($AdultCount) && isset($SessionId) && (preg_match('/^(fake-service-parto-8c8d-).*\b/', $SessionId) == true) &&
            isset($TravelPreference) && isset($OriginDestinationInformations) && is_array($OriginDestinationInformations)
        ) {
            $departureTime = $OriginDestinationInformations[0]['DepartureDateTime'];

            ///> check is there a flight with these parameters
            $flight = PartoFlightSearch::where('OriginLocationCode', $OriginDestinationInformations[0]['OriginLocationCode'])
                ->where('AirTripType', $TravelPreference['AirTripType'])
                ->where('DestinationLocationCode', $OriginDestinationInformations[0]['DestinationLocationCode'])->where('InfantCount', ($InfantCount ?? 0))->where('ChildCount', ($ChildCount ?? 0))->first();
            if (is_null($flight)) {
                if (($rand ?? false) == false) {
                    return response([
                        "Success" => false,
                        "Error" => [
                            "Id" => "Err0103016",
                            "Message" => "Flights not found for the given search condition"
                        ],
                        "PricedItineraries" => []
                    ], 422);
                }

                $searchResult = PartoFlightSearch::inRandomOrder()->first();
                $selected = PartoSelectedFlight::where('session_id', $session->id)->where('search_flight_id', $searchResult->id)->first();
                if (is_null($selected)) {
                    $selected = new PartoSelectedFlight();
                    $selected->search_flight_id = $searchResult->id;
                    $selected->session_id = $session->id;
                    $selected->save();
                }

                $searchResult->DepartureDateTime = $departureTime;
                $searchResult->update();
                return response($searchResult->response, 200, ['flight_search_parto_id' => $searchResult->id]);
            }

            ///> store selected flight
            $selected = PartoSelectedFlight::where('session_id', $session->id)->where('search_flight_id', $flight->id)->first();
            if (is_null($selected)) {
                $selected = new PartoSelectedFlight();
                $selected->search_flight_id = $flight->id;
                $selected->session_id = $session->id;
                $selected->save();
            }

            $flight->DepartureDateTime = $departureTime;
            $flight->update();
            return response($flight->response, 200);
        }

        return response('Unkown', 499);
    }

    public function book()
    {
        extract(request()->all());

        //validate the request
        $validated = Validator::make(request()->all(), [
            'FareSourceCode' => 'required',
            'SessionId' => 'required',
            'TravelerInfo' => 'required|array',
            'TravelerInfo.Email' => 'required|email',
            'TravelerInfo.PhoneNumber' => 'required',
            'TravelerInfo.AirTravelers' => 'required|array',
            'TravelerInfo.AirTravelers.*.PassengerType' => 'required|numeric', Rule::in(1, 2, 3),
            'TravelerInfo.AirTravelers.*.PassengerName' => 'required|array',
            'TravelerInfo.AirTravelers.*.PassengerName.PassengerFirstName' => 'required|string',
            'TravelerInfo.AirTravelers.*.PassengerName.PassengerLastName' => 'required|string',
            'TravelerInfo.AirTravelers.*.Nationality' => 'required|string',
        ], [
            'FareSourceCode.required' => 'FareSourceCode cannot be null,Err0107001',
            'SessionId.required' => 'SessionID cannot be null,Err0102007',
            'TravelerInfo.required' => 'TravelerInfo cannot be null,Err0107003',
            'TravelerInfo.array' => 'TravelerInfo cannot be null,Err0107003',
            'TravelerInfo.Email.required' => 'Email cannot be null,Err0107055',
            'TravelerInfo.Email.email' => 'Email is not valid,Err0107056',
            'TravelerInfo.PhoneNumber.required' => 'PhoneNumber cannot be null,Err0107006',
            'TravelerInfo.AirTravelers.required' => 'n ADULT given while search but m ADULT information present in booking request,Err0107028',
            'TravelerInfo.AirTravelers.*.PassengerType.required' => 'n ADULT given while search but m ADULT information present in booking request,Err0107028',
            'TravelerInfo.AirTravelers.*.PassengerType.numeric' => 'n ADULT given while search but m ADULT information present in booking request,Err0107028',
            'TravelerInfo.AirTravelers.*.PassengerType.in' => 'Passenger type is not valid,Err0107028',
            'TravelerInfo.AirTravelers.*.PassengerName.required' => 'PassengerName cannot be null,Err0107009',
            'TravelerInfo.AirTravelers.*.PassengerName.array' => 'PassengerName cannot be null,Err0107009',
            'TravelerInfo.AirTravelers.*.PassengerName.PassengerFirstName.required' => 'PassengerName cannot be null,Err0107009',
            'TravelerInfo.AirTravelers.*.PassengerName.PassengerFirstName.string' => 'PassengerName cannot be null,Err0107009',
            'TravelerInfo.AirTravelers.*.PassengerName.PassengerLastName.required' => 'PassengerName cannot be null,Err0107009',
            'TravelerInfo.AirTravelers.*.PassengerName.PassengerLastName.string' => 'PassengerName cannot be null,Err0107009',
            'TravelerInfo.AirTravelers.*.Nationality' => 'Nationality should be of format ^([A-Z][A-Z])$,Err0107026',
        ]);

        // if the request validation fails
        if ($validated->fails()) {
            return response([
                "Error" => [
                    "Id" => explode(',', $validated->messages()->first())[1],
                    "Message" => explode(',', $validated->messages()->first())[0],
                ],
                "Success" => false,
                "TktTimeLimit" => null,
                "Category" => null,
                "Status" => null,
                "UniqueId" => null,
                "PriceChange" => false
            ], 422);
        }

        ///> check session expire time
        $session = Session::where('sessionId', $SessionId)->first();
        if (isset($session) && $session->expired_at < Carbon::now()) {
            return response([
                "Error" => [
                    "Id" => "Err0102008",
                    "Message" => "Invalid SessionID"
                ],
                "Success" => false,
                "TktTimeLimit" => null,
                "Category" => null,
                "Status" => null,
                "UniqueId" => null,
                "PriceChange" => false
            ], 422);
        }

        ///> get selected flight informations
        $flight = PartoSelectedFlight::where('session_id', $session->id)->first();
        if (is_null($flight)) {
            return response([
                "Error" => [
                    "Id" => "Err0101003",
                    "Message" => "An unexpected error occurred while processing the flight. API Support notified!"
                ],
                "Success" => false,
                "TktTimeLimit" => null,
                "Category" => null,
                "Status" => null,
                "UniqueId" => null,
                "PriceChange" => false
            ], 422);
        }

        $response = $flight->search->response;

        ///> get posible fareSourceCodes of selected flight
        $FareSourceCodes = [];
        foreach ($response['PricedItineraries'] as $res) {
            $FareSourceCodes[] = $res['FareSourceCode'];
        }

        ///> check entered FareSourceCode is in posible FareSourceCodes
        if (!in_array($FareSourceCode, $FareSourceCodes)) {
            return response([
                "Error" => [
                    "Id" => "Err0101007",
                    "Message" => "An unexpected error occurred while processing the AirBook call. API Support notified!"
                ],
                "Success" => false,
                "TktTimeLimit" => null,
                "Category" => null,
                "Status" => null,
                "UniqueId" => null,
                "PriceChange" => false
            ], 422);
        }

        ///> If the flight was already booked
        $book = PartoBooking::where('passenger_name', $TravelerInfo['AirTravelers'][0]['PassengerName']['PassengerFirstName'])->where('passenger_lastname', $TravelerInfo['AirTravelers'][0]['PassengerName']['PassengerLastName'])->where('parto_flight_search_id', $flight['search_flight_id'])->where('session_id', $flight['session_id'])->first();
        if (!is_null($book)) {
            return response([
                "Error" => [
                    "Id" => "Err0107032",
                    "Message" => "There is duplicate booking for {0} under the booking no: {1}"
                ],
                "Success" => false,
                "TktTimeLimit" => $book->TktTimeLimit,
                "Category" => null,
                "Status" => null,
                "UniqueId" => null,
                "PriceChange" => false
            ], 422);
        }

        ///> successfully booked the flight
        if (isset($FareSourceCode) && isset($SessionId) && empty($book) && isset($SessionId)) {
            $array = [10, 20, 21];
            $index = array_rand($array, 1);
            $uniqueId = "fake-service-parto-uniqueId-" . uniqid();
            $booking = new PartoBooking();
            $booking->passenger_name = $TravelerInfo['AirTravelers'][0]['PassengerName']['PassengerFirstName'];
            $booking->passenger_lastname = $TravelerInfo['AirTravelers'][0]['PassengerName']['PassengerLastName'];
            $booking->parto_flight_search_id = $flight['search_flight_id'];
            $booking->TktTimeLimit = Carbon::tomorrow()->endOfDay();
            $booking->UniqueId = $uniqueId;
            $booking->Category = $array[$index];
            $booking->Status = $array[$index];
            $booking->session_id = $flight['session_id'];
            $booking->save();

            return response([
                "Error" => null,
                "Success" => true,
                "TktTimeLimit" => Carbon::tomorrow()->endOfDay(),
                "Category" => $array[$index],
                "Status" => $array[$index],
                "UniqueId" => $uniqueId,
                "PriceChange" => false
            ], 200);
        }

        return response('Unkown', 499);
    }

    public function orderTicket()
    {
        extract(request()->all());

        ///> validate request
        $validated = Validator::make(request()->all(), [
            'SessionId' => 'required',
            'UniqueId' => 'required',
        ], [
            'SessionId.required' => 'SessionID cannot be null,Err0102007',
            'UniqueId.required' => 'UniqueID cannot be null,Err0111001',
        ]);

        ///> validation fails
        if ($validated->fails()) {
            return response([
                "Error" => [
                    "Id" => explode(',', $validated->messages()->first())[1],
                    "Message" => explode(',', $validated->messages()->first())[0],
                ],
                "Success" => false,
                "Category" => null,
                "Status" => null
            ], 422);
        }

        ///> check session
        $session = Session::where('sessionId', $SessionId)->first();
        if (isset($session) && $session->expired_at < Carbon::now()) {
            return response([
                "Error" => [
                    "Id" => "Err0102008",
                    "Message" => "Invalid SessionID"
                ],
                "Success" => false,
                "Category" => null,
                "Status" => null
            ], 422);
        }

        ///> check book status
        $book = PartoBooking::where('UniqueId', $UniqueId)->first();
        if (is_null($book)) {
            return response([
                "Error" => [
                    "Id" => "Err0101003",
                    "Message" => "An unexpected error occurred while processing the AirLowFareSearch call. API Support notified!"
                ],
                "Success" => false,
                "Category" => null,
                "Status" => null
            ], 422);
        }

        if (isset($book) && $book->TktTimeLimit < Carbon::now()) {
            return response([
                "Error" => [
                    "Id" => "Err0111005",
                    "Message" => "IBooking may be already ordered for ticket or ticketed or cancelled"
                ],
                "Success" => false,
                "Category" => null,
                "Status" => null
            ], 422);
        }

        ///> The ticket has been ordered successfully
        if (isset($book) &&  $book->TktTimeLimit > Carbon::now() && isset($UniqueId) && isset($SessionId)) {
            return response([
                "Error" => null,
                "Success" => true,
                "Category" => 21,
                "Status" => 21
            ], 200);
        }

        return response('Unkown', 499);
    }

    public function bookingData()
    {
        extract(request()->all());

        ///> validate request
        $validated = Validator::make(request()->all(), [
            'SessionId' => 'required',
            'UniqueId' => 'required',
        ], [
            'SessionId.required' => 'SessionID cannot be null,Err0102007',
            'UniqueId.required' => 'UniqueID and ClientUniqueID cannot be both null,Err0109006',
        ]);

        ///> validation fails
        if ($validated->fails()) {
            return response([
                "Error" => [
                    "Id" => explode(',', $validated->messages()->first())[1],
                    "Message" => explode(',', $validated->messages()->first())[0],
                ],
                "UniqueId" => null,
                "FareType" => 0,
                "BookedBy" => null,
                "OrderBy" => null,
                "ClientBalance" => 0.0,
                "Success" => false,
                "TktTimeLimit" => null,
                "Category" => null,
                "Status" => null,
                "RefundMethod" => null,
                "TravelItinerary" => null,
                "ValidatingAirlineCode" => null,
                "DirectionInd" => 0
            ], 422);
        }

        ///> check session
        $session = Session::where('sessionId', $SessionId)->first();
        if (isset($session) && $session->expired_at < Carbon::now()) {
            return response([
                "Error" => [
                    "Id" => "Err0102008",
                    "Message" => "Invalid SessionID"
                ],
                "UniqueId" => null,
                "FareType" => 0,
                "BookedBy" => null,
                "OrderBy" => null,
                "ClientBalance" => 0.0,
                "Success" => false,
                "TktTimeLimit" => null,
                "Category" => null,
                "Status" => null,
                "RefundMethod" => null,
                "TravelItinerary" => null,
                "ValidatingAirlineCode" => null,
                "DirectionInd" => 0
            ], 422);
        }

        ///> check uniqueId
        $book = PartoBooking::where('UniqueId', $UniqueId)->where('session_id', $session->id)->first();
        if (is_null($book)) {
            return response([
                "Error" => [
                    "Id" => "Err0101003",
                    "Message" => "An unexpected error occurred while processing the AirLowFareSearch call. API Support notified!"
                ],
                "UniqueId" => null,
                "FareType" => 0,
                "BookedBy" => null,
                "OrderBy" => null,
                "ClientBalance" => 0.0,
                "Success" => false,
                "TktTimeLimit" => null,
                "Category" => null,
                "Status" => null,
                "RefundMethod" => null,
                "TravelItinerary" => null,
                "ValidatingAirlineCode" => null,
                "DirectionInd" => 0
            ], 422);
        }

        $passNumber = $book->search->AdultCount . $book->search->ChildCount . $book->search->InfantCount;
        $bookingData = PartoBookingData::where('passenger_number', $passNumber)->inRandomOrder()->first();
        $date = date('Y-m-d h:i:s', strtotime('-1 day', strtotime($book->search->DepartureDateTime)));

        $answer = $bookingData->response;
        $answer['TktTimeLimit'] = $date;

        if (isset($UniqueId) && isset($SessionId)) {
            return response($answer, 200);
        }
        return response('Unkown', 499);
    }

    public function fareRule()
    {
        extract(request()->all());

        ///> check request validation
        $validated = Validator::make(request()->all(), [
            'SessionId' => 'required',
            'FareSourceCode' => 'required'
        ], [
            'SessionId.required' => 'SessionID cannot be null,Err0102007',
            'FareSourceCode.required' => 'FareSourceCode or UniqueId cannot be null,Err0105001',
        ]);

        ///> if request validation fails
        if ($validated->fails()) {
            return response([
                "Error" => [
                    "Id" => explode(',', $validated->messages()->first())[1],
                    "Message" => explode(',', $validated->messages()->first())[0],
                ],
                "FareType" => null,
                "Success" => false,
                "FareRules" => []
            ], 422);
        }

        ///> check session
        $session = Session::where('sessionId', $SessionId)->first();
        if (isset($session) && $session->expired_at < Carbon::now()) {
            return response([
                "Error" => [
                    "Id" => "Err0102008",
                    "Message" => "Invalid SessionID"
                ],
                "FareType" => null,
                "Success" => false,
                "FareRules" => []
            ], 422);
        }

        $select = PartoSelectedFlight::where('session_id', $session->id)->first();
        if (is_null($select)) {
            return response([
                "Error" => [
                    "Id" => "Err0101003",
                    "Message" => "An unexpected error occurred while processing the AirLowFareSearch call. API Support notified!"
                ],
                "FareType" => null,
                "Success" => false,
                "FareRules" => []
            ], 422);
        }

        $flight = $select->search;

        if ($flight->AirTripType == 2) {
            $tripType = 'roundTrip';
        } else {
            $tripType = 'oneWay';
        }
        $filePath = storage_path("Farerules/parto/$tripType/1.json");
        $response = json_decode(file_get_contents($filePath), true);
        return response($response);

        return response('Unkown', 499);
    }
}
