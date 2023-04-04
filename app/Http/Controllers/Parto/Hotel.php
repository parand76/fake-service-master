<?php

namespace App\Http\Controllers\Parto;

use App\Http\Controllers\Controller;
use App\Models\PartoBookingData;
use App\Models\PartoHotelBooking;
use App\Models\PartoHotelBookingData;
use App\Models\PartoHotelCities;
use App\Models\PartoHotelDetail;
use App\Models\PartoHotels;
use App\Models\PartoHotelSearch;
use App\Models\PartoSelectedHotel;
use App\Models\Session;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Hotel extends Controller
{
    public function Availability()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            "SessionId" => "required",
            "CheckIn" => "required|date",
            "CheckOut" => "required|date",
            "CityId" => "required_if:RegionCode,empty|required_without:HotelId|integer",
            "HotelId" => "required_if:CityId,empty|required_without:CityId",
            "RegionCode" => "required_if:CityId,empty",
            "NationalityId" => "required",
            "Occupancies" => "required|array",
            "Occupancies.*.AdultCount" => "required|integer|min:1"
        ], [
            "SessionId.required" => "SessionID cannot be null,Err0102007",
            "CheckIn.required" => "Check In date cannot be in the past,Err0120012",
            "CheckIn.date" => "Check In date cannot be in the past,Err0120012",
            "CheckOut.required" => "Check Out date cannot be in the past,Err0120013",
            "CheckOut.date" => "Check Out date cannot be in the past,Err0120013",
            "CityId.required_if" => "Please set PropertyId or CityId or GeoLocation or RegionCode,Err0120004",
            "CityId.integer" => "Please set PropertyId or CityId or GeoLocation or RegionCode,Err0120004",
            "CityId.required_without" => "Use only one of CityId or HotelId,Err0120019",
            "HotelId.required_if" => "Use only one of CityId or HotelId,Err0120019",
            "HotelId.required_without" => "Use only one of CityId or HotelId,Err0120019",
            "RegionCode.required_if" => "Please set PropertyId or CityId or GeoLocation or RegionCode,Err0120004",
            "NationalityId.required" => "NationalityId cannot be null,Err0120011",
            "Occupancies.required" => "Occupancies cannot be null,Err0120003",
            "Occupancies.array" => "Occupancies cannot be null,Err0120003",
            "Occupancies.*.AdultCount.required" => "AdultCount should not be zero,Err0120018",
            "Occupancies.*.AdultCount.integer" => "AdultCount should not be zero,Err0120018",
            "Occupancies.*.AdultCount.min" => "AdultCount should not be zero,Err0120018",
        ]);

        ///> if request validation fails
        if ($validated->fails()) {
            return response([
                "Success" => false,
                "Error" => [
                    "id" => explode(",", $validated->messages()->first())[1],
                    "Message" => explode(",", $validated->messages()->first())[0],
                ],
                "CheckIn" => $CheckIn,
                "CheckOut" => $CheckOut,
                "PricedItineraries" => []
            ], 422);
        }

        ///> check session expire time
        $session = Session::where("sessionId", $SessionId)->first();
        if (isset($session) && $session->expired_at < Carbon::now()) {
            return response([
                "Success" => false,
                "Error" => [
                    "id" => "Err0102008",
                    "Message" => "Invalid SessionID"
                ],
                "CheckIn" => "0001-01-01T00:00:00",
                "CheckOut" => "0001-01-01T00:00:00",
                "PricedItineraries" => []
            ], 422);
        }

        ///> all count of adults
        $adultCount = 0;
        $childCount = 0;
        foreach ($Occupancies as $Occupancy) {
            $adultCount += $Occupancy["AdultCount"];
            $childCount += $Occupancy["ChildCount"];
        }

        ///>isset CityId - user is searching
        if (isset($CityId)) {

            $isCityExists = PartoHotelCities::where("id", $CityId)->first();
            if (is_null($isCityExists)) {
                return response([
                    "Success" => false,
                    "Error" => [
                        "id" => "Err0120001",
                        "Message" => "City not found!"
                    ],
                    "CheckIn" => "0001-01-01T00:00:00",
                    "CheckOut" => "0001-01-01T00:00:00",
                    "PricedItineraries" => []
                ], 422);
            }

            ///> select hotels for $cityId
            $HotelsId = PartoHotels::where("propertyCityId", $CityId)->get(["id"]);
            $allHotelIds = [];
            foreach ($HotelsId as $id) {
                array_push($allHotelIds, $id->id);
            }

            $checkHotel = PartoHotelSearch::where("CheckIn", $CheckIn)->where("CheckOut", $CheckOut)->where("AdultCount", $adultCount)->where("ChildCount", $childCount)->whereIn("HotelId", $allHotelIds)->whereNotNull('AvailableRooms')->get();
        }

        ///> isset HotelId - user choose a hotel
        if (isset($HotelId)) {
            $checkHotel = PartoHotelSearch::where("CheckIn", $CheckIn)->where("CheckOut", $CheckOut)->where("AdultCount", $adultCount)->where("ChildCount", $childCount)->where("HotelId", $HotelId)->whereNotNull('AvailableRooms')->get();
        }

        ///> hotel result
        if (count($checkHotel) == 0) {
            return response([
                "Success" => false,
                "Error" => [
                    "id" => "Err0120001",
                    "Message" => "Hotels/Rooms not found for the given search condition"
                ],
                "CheckIn" => "0001-01-01T00:00:00",
                "CheckOut" => "0001-01-01T00:00:00",
                "PricedItineraries" => []
            ], 422);
        }

        ///> store selected
        $Hotel = isset($HotelId) ? $HotelId : json_decode($checkHotel[0]->responses, true)['PricedItineraries'][0]['HotelId'];
        $FareSourceCode = json_decode($checkHotel[0]->responses, true)['PricedItineraries'][0]['FareSourceCode'];
        $CheckInSelectDb = PartoSelectedHotel::where('SessionId', $SessionId)->where('HotelId', $Hotel)->where('FareSourceCode', $FareSourceCode)->first();
        if (is_null($CheckInSelectDb)) {
            $select = new PartoSelectedHotel();
            $select->SessionId = $SessionId;
            $select->HotelId = $Hotel;
            $select->FareSourceCode = $FareSourceCode;
            $select->HotelDetails = json_encode(json_decode($checkHotel[0]->responses, true)['PricedItineraries'][0]);
            $select->save();
        }

        return response(json_decode($checkHotel[0]->responses, true), 200)->header("Content-Type", "application/json");
    }

    public function CheckRate()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            "SessionId" => "required",
            "FareSourceCode" => "required"
        ], [
            "SessionId.required" => "SessionID cannot be null,Err0102007",
            "FareSourceCode.required" => "FareSourceCode cannot be null,Err0102007"
        ]);

        ///> if request fails
        if ($validated->fails()) {
            return response([
                "Success" => false,
                "Error" => [
                    "id" => explode(",", $validated->messages()->first())[1],
                    "Message" => explode(",", $validated->messages()->first())[0]
                ]
            ], 422);
        }

        ///> check session expire time
        $session = Session::where("sessionId", $SessionId)->first();
        if (isset($session) && $session->expired_at < Carbon::now()) {
            return response([
                "Success" => false,
                "Error" => [
                    "id" => "Err0102008",
                    "Message" => "Invalid SessionID"
                ],
                "CheckIn" => "0001-01-01T00:00:00",
                "CheckOut" => "0001-01-01T00:00:00",
                "PricedItineraries" => []
            ], 422);
        }

        ///> check infos
        $findHotel = PartoSelectedHotel::where('SessionId', $SessionId)->where('FareSourceCode', $FareSourceCode)->first();
        if (is_null($findHotel)) {
            return response([
                "Success" => false,
                "Error" => [
                    "id" => "Err0102008",
                    "Message" => "Hotel not found"
                ]
            ], 422);
        }

        ///> hotel result
        $Details = json_decode($findHotel['HotelDetails'], true);
        $minValue = 10.5;
        $maxValue = 99999.5;
        return response([
            "Success" => true,
            "Error" => null,
            "CheckIn" => "2022-04-01T00:00:00",
            "CheckOut" => "2022-04-02T00:00:00",
            "ClientBalance" => round($minValue + mt_rand() / mt_getrandmax() * ($maxValue - $minValue), 2),
            "PricedItinerary" => $Details,
        ], 200)->header("Content-Type", "application/json");
    }

    public function Book()
    {
        extract(request()->all());

        ///> check request validation
        $validated = Validator::make(request()->all(), [
            "SessionId" => "required",
            "FareSourceCode" => "required",
            "ClientUniqueId" => "required",
            "PhoneNumber" => "required",
            "Email" => "required|email",
            "Rooms" => "required|array",
            "Rooms.*.Passengers" => "required",
            "Rooms.*.Passengers.*.PassengerType" => Rule::in([1, 2, 3])
        ], [
            "SessionId.required" => "SessionID cannot be null,Err0102007",
            "FareSourceCode.required" => "Please call Book method after CheckRate,Err0122004",
            "ClientUniqueId.required" => "Please call Book method after CheckRate,Err0122004",
            "PhoneNumber.required" => "PhoneNumber is required,Err0121001",
            "Email.required" => "PhoneNumber is required,Err0121001",
            "Email.email" => "email is required,Err0121001",
            "Rooms.required" => "room is required,Err0121001",
            "Rooms.array" => "room is required,Err0121001",
            "Rooms.*.Passengers.required" => "n Adult given while search but m Adult information present in booking request,Err0123010",
            "Rooms.*.Passengers.*.PassengerType.in" => "Invalid PassengerType,Err0123010"
        ]);

        ///> request validation fails
        if ($validated->fails()) {
            return response([
                "Success" => false,
                "PaymentDeadline" => "0001-01-01T00:00:00",
                "UniqueId" => null,
                "Error" => [
                    "Id" => explode(",", $validated->messages()->first())[1],
                    "Message" => explode(",", $validated->messages()->first())[0]
                ],
                "VatNumber" => null,
                "SupplierName" => null,
                "Remarks" => [],
                "Category" => null,
                "Status" => null,
                "CancelationPolicyChange" => false,
                "CanExtendPaymentDeadline" => false
            ], 422);
        }

        ///> check session expire time
        $session = Session::where("sessionId", $SessionId)->first();
        if (isset($session) && $session->expired_at < Carbon::now()) {
            return response([
                "Success" => false,
                "PaymentDeadline" => "0001-01-01T00:00:00",
                "UniqueId" => null,
                "Error" => [
                    "Id" => "Err0102008",
                    "Message" => "Invalid SessionID"
                ],
                "VatNumber" => null,
                "SupplierName" => null,
                "Remarks" => [],
                "Category" => null,
                "Status" => null,
                "CancelationPolicyChange" => false,
                "CanExtendPaymentDeadline" => false
            ], 422);
        }

        ///> check FareSourceCode
        $findHotel = PartoSelectedHotel::where('SessionId', $SessionId)->where('FareSourceCode', $FareSourceCode)->first();
        if (is_null($findHotel)) {
            return response([
                "Error" => [
                    "Id" => "Err0122004",
                    "Message" => "Hotel not found"
                ],
                "Status" => null,
                "Remarks" => [],
                "Success" => false,
                "Category" => null,
                "UniqueId" => null,
                "VatNumber" => null,
                "SupplierName" => null,
                "PaymentDeadline" => "0001-01-01T00:00:00",
                "CancelationPolicyChange" => false,
                "CanExtendPaymentDeadline" => false
            ], 422);
        }

        ///> check passengers count
        $adultCount = 0;
        $childCount = 0;
        foreach ($Rooms[0]['Passengers'] as $passenger) {
            if ($passenger['PassengerType'] == 1) {
                $adultCount++;
            } else {
                $childCount++;
            }
        }

        $checksearchParams = PartoHotelSearch::where('FareSourceCode', $FareSourceCode)->where('AdultCount', $adultCount)->where('ChildCount', $childCount)->first();
        if (is_null($checksearchParams)) {
            return response([
                "Error" => [
                    "Id" => "Err0123010",
                    "Message" => "n Adult given while search but m Adult information present in booking request"
                ],
                "Status" => null,
                "Remarks" => [],
                "Success" => false,
                "Category" => null,
                "UniqueId" => null,
                "VatNumber" => null,
                "SupplierName" => null,
                "PaymentDeadline" => "0001-01-01T00:00:00",
                "CancelationPolicyChange" => false,
                "CanExtendPaymentDeadline" => false
            ], 422);
        }

        ///> hotel ordered before
        $findBookInfo = PartoHotelBooking::where('PhoneNumber', $PhoneNumber)->where('Email', $Email)->whereNotNull('UniqueId')->where('SessionId', $SessionId)->where('FareSourceCode', $FareSourceCode)->where('ClientUniqueId', $ClientUniqueId)->first();
        if (!is_null($findBookInfo)) {
            return response([
                "Success" => false,
                "PaymentDeadline" => "0001-01-01T00:00:00",
                "UniqueId" => null,
                "Error" => [
                    "Id" => "Err0107032",
                    "Message" => "There is duplicate booking"
                ],
                "VatNumber" => null,
                "SupplierName" => null,
                "Remarks" => [],
                "Category" => null,
                "Status" => null,
                "CancelationPolicyChange" => false,
                "CanExtendPaymentDeadline" => false
            ], 422);
        }

        ///> book the hotel
        $array = [10, 20, 21];
        $index = array_rand($array, 1);
        $uniqueId =  uniqid("PH000");
        $booking = new PartoHotelBooking();
        $booking->SessionId = $SessionId;
        $booking->FareSourceCode = $FareSourceCode;
        $booking->ClientUniqueId = $ClientUniqueId;
        $booking->PhoneNumber = $PhoneNumber;
        $booking->Email = $Email;
        $booking->Rooms = json_encode($Rooms);
        $booking->UniqueId = $uniqueId;
        $booking->PaymentDeadline = Carbon::tomorrow()->endOfDay();
        $booking->Category = $array[$index];
        $booking->Status = $array[$index];
        $booking->responses = json_encode([
            "Success" => true,
            "PaymentDeadline" => Carbon::tomorrow()->endOfDay(),
            "UniqueId" => $uniqueId,
            "Error" => null,
            "VatNumber" => null,
            "SupplierName" => "",
            "Remarks" => [
                "test remark",
                "<a target=\"_blank\" href=\"https://developer.ean.com/terms/agent/en/\">Agent Terms and Conditions</a>",
                "<a target=\"_blank\" href=\"https://developer.ean.com/terms/en\">Supplier Terms of use</a>"
            ],
            "Category" => $array[$index],
            "Status" => $array[$index],
            "CancelationPolicyChange" => false,
            "CanExtendPaymentDeadline" => false
        ]);
        $booking->save();

        return response([
            "Success" => true,
            "PaymentDeadline" => Carbon::tomorrow()->endOfDay(),
            "UniqueId" => $uniqueId,
            "Error" => null,
            "VatNumber" => null,
            "SupplierName" => "",
            "Remarks" => [
                "test remark",
                "<a target=\"_blank\" href=\"https://developer.ean.com/terms/agent/en/\">Agent Terms and Conditions</a>",
                "<a target=\"_blank\" href=\"https://developer.ean.com/terms/en\">Supplier Terms of use</a>"
            ],
            "Category" => $array[$index],
            "Status" => $array[$index],
            "CancelationPolicyChange" => false,
            "CanExtendPaymentDeadline" => false
        ], 200);
    }

    public function Order()
    {
        extract(request()->all());

        ///> check request validtion
        $validated = Validator::make(request()->all(), [
            "SessionId" => "required",
            "UniqueId" => "required"
        ], [
            "SessionId.required" => "SessionID cannot be null,Err0102007",
            "UniqueId.required" => "UniqueId cannot be null,Err0102007"
        ]);

        if ($validated->fails()) {
            return response([
                "Error" => [
                    "Id" => explode(",", $validated->messages()->first())[1],
                    "Message" => explode(",", $validated->messages()->first())[0]
                ],
                "Success" => false,
                "Category" => null,
                "Status" => null
            ], 422);
        }

        ///> check session expire time
        $session = Session::where("sessionId", $SessionId)->first();
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

        ///> check UniqueId
        $findBookInfo = PartoHotelBooking::where('UniqueId', $UniqueId)->first();
        if (is_null($findBookInfo)) {
            return response([
                "Error" => [
                    "Id" => "Err0102008",
                    "Message" => "Invalid UniqueId"
                ],
                "Success" => false,
                "Category" => null,
                "Status" => null
            ], 422);
        }

        ///> check book status
        if (isset($findBookInfo) && $findBookInfo->PaymentDeadline < Carbon::now()) {
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
        if (isset($findBookInfo) &&  $findBookInfo->PaymentDeadline > Carbon::now()) {
            return response([
                "Error" => null,
                "Success" => true,
                "Category" => 21,
                "Status" => 21
            ]);
        }
    }

    public function BookingData()
    {
        extract(request()->all());

        ///> check request validation
        $validated = Validator::make(request()->all(), [
            "SessionId" => "required",
            "UniqueId" => "required"
        ], [
            "SessionId.required" => "SessionID cannot be null,Err0102007",
            "UniqueId.required" => "UniqueId cannot be null,Err0102007"
        ]);

        ///> if validaion fails
        if ($validated->fails()) {
            return response([
                "Error" => [
                    "Id" => explode(",", $validated->messages()->first())[1],
                    "Message" => explode(",", $validated->messages()->first())[0]
                ],
                "Success" => false,
                "Category" => null,
                "Status" => null
            ], 422);
        }

        ///> check session expire time
        $session = Session::where("sessionId", $SessionId)->first();
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

        ///> check UniqueId
        $findBookInfo = PartoHotelBooking::where('UniqueId', $UniqueId)->first();
        if (is_null($findBookInfo)) {
            return response([
                "Error" => [
                    "Id" => "Err0102008",
                    "Message" => "Invalid UniqueId"
                ],
                "Success" => false,
                "Category" => null,
                "Status" => null
            ], 422);
        }

        ///> hotel infos
        $hotelInfo = PartoHotelSearch::where('FareSourceCode', $findBookInfo->FareSourceCode)->first();
        if (is_null($hotelInfo)) {
            return response([
                "Error" => [
                    "Id" => "Err0102008",
                    "Message" => "Invalid hotel informations"
                ],
                "Success" => false,
                "Category" => null,
                "Status" => null
            ], 422);
        }

        $BookingData = PartoHotelBookingData::where('UniqueId', $UniqueId)->first();
        if (is_null($BookingData)) {
            $array = [10, 20, 21];
            $index = array_rand($array, 1);
            $bookdata = new PartoHotelBookingData();
            $bookdata->UniqueId = $UniqueId;
            $bookdata->status = $array[$index];

            $res = json_decode($hotelInfo->responses, true);
            $NetRate = $res['PricedItineraries'][0]['NetRate'];
            $passengerCount = count(json_decode($findBookInfo->Rooms, true)[0]['Passengers']);
            $TotalNetRate = $NetRate * $passengerCount;
            $minValue = 10.5;
            $maxValue = 99999.5;
            $data = [
                "BookedBy" => "API ",
                "OrderBy" => "API ",
                "ClientBalance" => round($minValue + mt_rand() / mt_getrandmax() * ($maxValue - $minValue), 2),
                "Error" => null,
                "Success" => true,
                "PaymentDeadline" => "2021-02-18T09:44:30",
                "Category" =>  $findBookInfo->Category,
                "Status" => $findBookInfo->Status,
                "UniqueId" => $findBookInfo->UniqueId,
                "VatNumber" => null,
                "FareSourceCode" => $findBookInfo->FareSourceCode,
                "SupplierName" => "",
                "TotalNetRate" => $TotalNetRate,
                "Currency" => $res['PricedItineraries'][0]['NetRate'],
                "CheckIn" => $res['CheckIn'],
                "CheckOut" => $res['CheckOut'],
                "Rooms" => [
                    "RoomArchiveId" => uniqid("9e83cd8a", true),
                    "Name" => $res['PricedItineraries'][0]['Rooms'][0]['Name'],
                    "MealType" => $res['PricedItineraries'][0]['Rooms'][0]['MealType'],
                    "SharingBedding" => $res['PricedItineraries'][0]['Rooms'][0]['SharingBedding'],
                    "BedGroups" => $res['PricedItineraries'][0]['Rooms'][0]['BedGroups'],
                    "Passengers" => json_decode($findBookInfo['Rooms'], true)[0]['Passengers'],
                    "HotelRoomEarlyCheckin" => null,
                    "HotelRoomLateCheckout" => null
                ],
                "Amenities" => $res['PricedItineraries'][0]['Amenities'],
                "HotelConfirmationNo" => null,
                "Remarks" => $res['PricedItineraries'][0]['Remarks'],
                "CancellationPolicies" => [],
                "PlainTextCancellationPolicy" => null,
                "NonRefundable" => $res['PricedItineraries'][0]['NonRefundable'],
                "HotelRefundType" => $res['PricedItineraries'][0]['HotelRefundType'],
                "PhoneNumber" => $findBookInfo->PhoneNumber,
                "Email" => $findBookInfo->Email,
                "CountryCode" => "GB",
                "IsReserveOffline" => $res['PricedItineraries'][0]['IsReserveOffline'],
                "HotelId" => $res['PricedItineraries'][0]['HotelId'],
                "NationalityId" => "US"
            ];

            $bookdata->responses = json_encode($data);
            $bookdata->save();
        }

        return response(json_decode($BookingData->responses,true), 200)->header('Content-Type', 'application/json');
    }

    public function Image()
    {
        extract(request()->all());

        ///> check request validation
        $validated = Validator::make(request()->all(), [
            "SessionId" => "required",
            "HotelId" => "required"
        ], [
            "SessionId.required" => "SessionID cannot be null,Err0102007",
            "HotelId" => "HotelId cannot be null,Err0120019"
        ]);

        ///> validation fails
        if ($validated->fails()) {
            return response([
                "Success" => false,
                "Error" => [
                    "id" => explode(",", $validated->messages()->first())[1],
                    "Message" => explode(",", $validated->messages()->first())[0],
                ],
                "CheckIn" => $CheckIn,
                "CheckOut" => $CheckOut,
                "PricedItineraries" => []
            ],422);
        }

        ///> check session expire time
        $session = Session::where("sessionId", $SessionId)->first();
        if (isset($session) && $session->expired_at < Carbon::now()) {
            return response([
                "Success" => false,
                "Error" => [
                    "id" => "Err0102008",
                    "Message" => "Invalid SessionID"
                ],
                "CheckIn" => "0001-01-01T00:00:00",
                "CheckOut" => "0001-01-01T00:00:00",
                "PricedItineraries" => []
            ],422);
        }

        ///>check if hotel has a image
        $checkHotelId = PartoHotelDetail::where('HotelId', $HotelId)->get();
        if (is_null($checkHotelId)) {
            return response([
                "Error" => [
                    "Id" => "Err0101001",
                    "Message" => "Invalid HotelId"
                ],
                "Success" => true,
                "Links" => []
            ], 422);
        }

        $allLinks = [];
        foreach ($checkHotelId as $res) {
            $links = json_decode($res->responses, true)['Links'];
            foreach ($links as $link) {
                if (str_contains($link['Link'], 'hotelimages')) {
                    if (!in_array($link, $allLinks)) {
                        array_push($allLinks, $link);
                    }
                }
            }
        }

        return response([
            "Error" => null,
            "Success" => true,
            "Links" => $allLinks
        ], 200)->header('Content-Type', 'application/json');
    }

    public function DomesticHotelImage()
    {
        extract(request()->all());

        ///> check request validation
        $validated = Validator::make(request()->all(), [
            "SessionId" => "required",
            "HotelId" => "required"
        ], [
            "SessionId.required" => "SessionID cannot be null,Err0102007",
            "HotelId" => "HotelId cannot be null,Err0120019"
        ]);

        ///> validation fails
        if ($validated->fails()) {
            return response([
                "Success" => false,
                "Error" => [
                    "id" => explode(",", $validated->messages()->first())[1],
                    "Message" => explode(",", $validated->messages()->first())[0],
                ],
                "CheckIn" => $CheckIn,
                "CheckOut" => $CheckOut,
                "PricedItineraries" => []
            ]);
        }

        ///> check session expire time
        $session = Session::where("sessionId", $SessionId)->first();
        if (isset($session) && $session->expired_at < Carbon::now()) {
            return response([
                "Success" => false,
                "Error" => [
                    "id" => "Err0102008",
                    "Message" => "Invalid SessionID"
                ],
                "CheckIn" => "0001-01-01T00:00:00",
                "CheckOut" => "0001-01-01T00:00:00",
                "PricedItineraries" => []
            ]);
        }

        ///> check if hotel has a image
        $checkHotelId = PartoHotelDetail::where('HotelId', $HotelId)->get();
        if (count($checkHotelId) == 0) {
            return response([
                "Error" => [
                    "Id" => "Err0101001",
                    "Message" => "Invalid HotelId"
                ],
                "Success" => true,
                "Links" => []
            ], 422);
        }

        $allLinks = [];
        foreach ($checkHotelId as $res) {
            $links = json_decode($res->responses, true)['Links'];
            foreach ($links as $link) {
                if (str_contains($link['Link'], 'hotelimagesdomestic')) {
                    if (!in_array($link, $allLinks)) {
                        array_push($allLinks, $link);
                    }
                }
            }
        }

        return response([
            "Error" => null,
            "Success" => true,
            "Links" => $allLinks
        ], 200)->header('Content-Type', 'application/json');
    }
}
