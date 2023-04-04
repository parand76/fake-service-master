<?php

namespace App\Http\Controllers\Sepehr;

use App\Http\Controllers\Controller;
use App\Models\SepehrFlightBooking;
use App\Models\SepehrFlightSearch;
use App\Models\SepehrSelectedFlight;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SepehrFlight extends Controller
{
    public function generateUUID($length)
    {
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= rand(0, 1) ? rand(0, 9) : chr(rand(ord('a'), ord('z')));
        }
        return $random;
    }

    public function Search()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            "UserName" => ['required', Rule::in(['tafaroj1'])],
            "Password" => ['required', Rule::in([md5('gasht')])],
            "OriginIataCode" => "required",
            "DestinationIataCode" => "required",
            "DepartureDate" => "required",
            "DepartureDateWindow" => "required",
            "FetchSupplierWebserviceFlights" => "required",
            "Language" => "required",
        ]);

        ///> request validation fails
        if ($validated->fails()) {
            return response([
                "Success"  => false,
                "Error" => [
                    "Message" => $validated->messages()->first()
                ]
            ]);
        }

        ///> search flight
        $FindFlight = SepehrFlightSearch::where('OriginCode', $OriginIataCode)->where('DestinationCode', $DestinationIataCode)->where('DepartureDateTime', 'LIKE', "%$DepartureDate%")->first();
        if (is_null($FindFlight)) {
            return response([
                "CurrencyCode" => "",
                "CharterFlights" => [],
                "WebserviceFlights" => [],
            ]);
        }

        ///> selected flight informations
        $Flight = json_decode(!empty($FindFlight['CharterFlights']) ? $FindFlight['CharterFlights'] : (!empty($FindFlight['WebserviceFlights']) ? $FindFlight['WebserviceFlights'] : null), true);
        if (is_null($Flight)) {
            return response([
                "CurrencyCode" => "",
                "CharterFlights" => [],
                "WebserviceFlights" => [],
            ]);
        }
        $FlightSegment = $Flight;
        $FlightNumber = $Flight[0]['FlightNumber'];
        $FareName = $Flight[0]['Classes'][0]['FareName'];
        $CurrencyCode = $FindFlight['CurrencyCode'];

        ///> store selected flight
        $User = "fake-service-sepehr-" . $Password;
        $selected =  SepehrSelectedFlight::where('user', $User)->where('FlightNumber', $FlightNumber)->where('FareName', $FareName)->first();
        if (is_null($selected)) {
            $selected = new SepehrSelectedFlight();
            $selected->user = $User;
            $selected->DepartureDateTime = $FindFlight['DepartureDateTime'];
            $selected->FlightNumber = $FlightNumber;
            $selected->FareName = $FareName;
            $selected->FlightSegment = json_encode($FlightSegment);
            $selected->CurrencyCode = $CurrencyCode;
            $selected->save();
        }

        return response([
            "CurrencyCode" => $CurrencyCode,
            "CharterFlights" => json_decode($FindFlight['CharterFlights'], true),
            "WebserviceFlights" => json_decode($FindFlight['WebserviceFlights'], true),
        ], 200);
    }

    public function Booking()
    {
        $response = DB::table('tmp')->where('message', 'LIKE', '%Supplier Response - SepehrBookAndIssueFlight%')->get();
        $insert = [];
        foreach ($response as $res) {

            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
            $body = $item['body'];
            $array = json_decode($body, true);
            $insert [] = $array;
        }

        return $insert;
        
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            "UserName" => ["required", Rule::in(['tafaroj1'])],
            "Password" => ["required", Rule::in([md5('gasht')])],
            "DepartureSegment.OriginIataCode" => "required",
            "DepartureSegment.DestinationIataCode" => "required",
            "DepartureSegment.DepartureDateTime" => "required",
            "DepartureSegment.FlightNumber" => "required",
            "DepartureSegment.FareName" => "required",
            "AdultPassengers" => "required|array",
            "AdultPassengers.*.FirstName" => "required",
            "AdultPassengers.*.LastName" => "required",
            "AdultPassengers.*.BirthDate" => "required",
            "AdultPassengers.*.Passport.Number" => "required_without:AdultPassengers.*.IranianCartMelli",
            "AdultPassengers.*.Passport.NationalityCountryCode" => "required_without:AdultPassengers.*.IranianCartMelli",
            "AdultPassengers.*.Passport.PlaceOfIssueCountryCode" => "required_without:AdultPassengers.*.IranianCartMelli",
            "AdultPassengers.*.Passport.ExpiryDate" => "required_without:AdultPassengers.*.IranianCartMelli",
            "AdultPassengers.*.IranianCartMelli.CodeMelli" => "required_without:AdultPassengers.*.Passport",
            "MobileNumber" => "required",
            "Email" => "required",
            "TotalPayable" => "required",

        ]);

        ///> validation fails
        if ($validated->fails()) {
            return response([
                "Pnr" => null,
                "Passengers" => null,
                "Error" => [
                    "Messages" => $validated->messages()->first(),
                ]
            ]);
        }

        ///> check flight
        $CheckFlight =  SepehrSelectedFlight::where('user', "fake-service-sepehr-" . $Password)->where('FlightNumber', $DepartureSegment['FlightNumber'])->where('FareName', $DepartureSegment['FareName'])->where('DepartureDateTime', $DepartureSegment['DepartureDateTime'])->orderByDesc('id')->first();
        if (is_null($CheckFlight)) {
            return response([
                "Pnr" => null,
                "Passengers" => null,
                "Error" => [
                    "Messages" => "Invalid flight information",
                ]
            ]);
        }

        $FlightSegment = json_decode($CheckFlight['FlightSegment'], true)[0];

        ///> get all total pay
        $AdultFare = 0;
        $ChildFare = 0;
        $InfantFare = 0;
        $AvailSeats = 0;
        foreach ($FlightSegment['Classes'] as $class) {
            if ($class['FareName'] == $DepartureSegment['FareName']) {
                $AdultFare = $class['AdultFare']['Payable'];
                $ChildFare = $class['ChildFare']['Payable'];
                $InfantFare = $class['InfantFare']['Payable'];
                $AvailSeats = $class['AvailableSeat'];
            }
        }
        if ($AdultFare == 0  || $ChildFare == 0 || $InfantFare == 0) {
            return response([
                "Pnr" => null,
                "Passengers" => null,
                "Error" => [
                    "Messages" => "Invalid flight information",
                ]
            ]);
        }

        ///> calcute total fare
        $AdultTotalFare = count($AdultPassengers) * $AdultFare;
        $ChildTotalFare = isset($ChildPassengers) ? count($ChildPassengers) * $ChildFare : 0;
        $InfantTotalFare = isset($InfantPassengers) ? count($InfantPassengers) * $InfantFare : 0;
        $TotalPassCount = $AdultTotalFare + $ChildTotalFare + $InfantTotalFare;
        if (count($AdultPassengers) == 0) {
            return response([
                "Pnr" => null,
                "Passengers" => null,
                "Error" => [
                    "Messages" => "At least one adult should be in flight",
                ]
            ]);
        }

        ///> check total pay
        if ($TotalPayable != $TotalPassCount) {
            return response([
                "Pnr" => null,
                "Passengers" => null,
                "Error" => [
                    "Messages" => "An error occure, please search again",
                ]
            ]);
        }

        ///> check available seats
        $AdultCount = count($AdultPassengers);
        $ChildCount = isset($ChildPassengers) ? count($ChildPassengers) : 0;
        $InfantCount = isset($InfantPassengers) ? count($InfantPassengers) : 0;
        $PassCounts = $AdultCount + $ChildCount + $InfantCount;
        if ($AvailSeats < $PassCounts) {
            return response([
                "Pnr" => null,
                "Passengers" => null,
                "Error" => [
                    "Messages" => "No seats available",
                ]
            ]);
        }

        ///> passengers infos
        $Passengers = [];
        foreach ($AdultPassengers as $adult) {
            $Passenger['FirstName'] = $adult['FirstName'];
            $Passenger['LastName'] = $adult['LastName'];
            $Passenger['DepartureSegment']['TicketNumber'] = rand(1000000, 9999999);
            $Passenger['DepartureSegment']['FlightNumber'] = $DepartureSegment['FlightNumber'];
            $Passenger['DepartureSegment']['FlightDate'] = $DepartureSegment['DepartureDateTime'];
            $Passenger['DepartureSegment']['OriginIataCode'] = $DepartureSegment['OriginIataCode'];
            $Passenger['DepartureSegment']['DestinationIataCode'] = $DepartureSegment['DestinationIataCode'];
            $Passenger['DepartureSegment']['Supplier'] = null;
            $Passenger['ReturningSegment'] = null;
            $Passengers[] = $Passenger;
        }

        ///> check if already booked or not
        $DepartureDateTime = $DepartureSegment['DepartureDateTime'];
        $IfBooked = SepehrFlightBooking::where('AdultCount', $AdultCount)->where('ChildCount', $ChildCount)->where('InfantCount', $InfantCount)->where('FlightNumber', $DepartureSegment['FlightNumber'])->where('FareName', $DepartureSegment['FareName'])->where('user', "fake-service-sepehr-" . $Password)->where('DepartureDateTime', 'LIKE', "%$DepartureDateTime%")->orderByDesc('id')->first();
        if (!is_null($IfBooked)) {
            return response([
                "Pnr" => null,
                "Passengers" => null,
                "Error" => [
                    "Messages" => "Already booked",
                ]
            ]);
        }

        ///> generate a pnr
        $pnr = strtoupper($this->generateUUID(6));
        $booking = new SepehrFlightBooking();
        $booking->user = "fake-service-sepehr-" . $Password;
        $booking->FlightNumber = $DepartureSegment['FlightNumber'];
        $booking->FareName = $DepartureSegment['FareName'];
        $booking->DepartureDateTime = $DepartureSegment['DepartureDateTime'];
        $booking->AdultCount = $AdultCount;
        $booking->ChildCount = $ChildCount;
        $booking->InfantCount = $InfantCount;
        $booking->Passengers = json_encode($Passengers);
        $booking->Pnr = $pnr;
        $booking->TotalFare = $TotalPassCount;
        $booking->save();

        return response([
            "Pnr" => $pnr,
            "Passengers" => $Passengers
        ], 200);
    }

    public function CurrentBalance()
    {
        extract(request()->all());
        // return request()->all();

        ///> request validation
        $validated = Validator::make(request()->all(), [
            "POS.Source.RequestorID.MessagePassword" => ['required', Rule::in([md5('gasht')])],
            "POS.Source.RequestorID.Name" => ['required', Rule::in(['tafaroj1'])],
        ]);

        $Username = $POS['Source']['RequestorID']['Name'];

        ///> validation fails
        if ($validated->fails()) {
            return response([
                "ErrorMessage" => "User '$Username' does not exist.",
                "ExceptionType" => "InvalidCredentialException",
                "TraceId" => "000-000-000"
            ]);
        }

        $minValue = 10.5;
        $maxValue = 99999.5;
        return response([
            "RemainedCredit" => round($minValue + mt_rand() / mt_getrandmax() * ($maxValue - $minValue), 2),
            "CurrencyCode" => "USD",
            "PaymentDueMinutes" => null
        ]);
    }
}
