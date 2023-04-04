<?php

namespace App\Http\Controllers\Avtra;

use App\Http\Controllers\Controller;
use App\Models\AvtraSearch;
use App\Models\FlyBaghdadBook;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie as FacadesCookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Cookie;

class Avtra extends Controller
{
    protected $search = [];
    public function lowFareSearch()
    {
        extract(request()->all());
        $data = request()->all();
        // dd($data['TravelerInfoSummary']['AirTravelerAvail']);
        // dd($data['OriginDestinationInformation'][0]['DestinationLocation']['@attributes']['LocationCode']);
        $this->searchValidate(request()->all());
        if (isset($data['OriginDestinationInformation'][0])) {
            $this->search = [
                'departureGo' => $data['OriginDestinationInformation'][1]['DepartureDateTime'],
                'OriginLocationCodeGo' => $data['OriginDestinationInformation'][0]['OriginLocation']['@attributes']['LocationCode'],
                'arivelLocationCodeGo' => $data['OriginDestinationInformation'][0]['DestinationLocation']['@attributes']['LocationCode'],
                'curency' => $data['POS']['Source']['@attributes']['ISOCurrency'],
                'departureBack' => $data['OriginDestinationInformation'][1]['DepartureDateTime'],
                'OriginLocationCodeBack' => $data['OriginDestinationInformation'][1]['OriginLocation']['@attributes']['LocationCode'],
                'arivelLocationCodeBack' => $data['OriginDestinationInformation'][1]['DestinationLocation']['@attributes']['LocationCode'],
            ];
            $timeGo = Carbon::create(randomDateTime(Carbon::create($this->search['departureGo'])->addMinutes(10 * 2)));
            $endGo = Carbon::parse($timeGo)->addHours(2);
            $timeBack = Carbon::create(randomDateTime(Carbon::create($this->search['departureBack'])->addMinutes(10 * 2)));
            $endBack = Carbon::parse($timeBack)->addHours(2);
            $option = [
                'DepartureDateTime' =>  $timeGo->toIso8601String(),
                'ArrivalDateTime' => $endGo->toIso8601String(),
                'FlightNumber' => 'fake' . rand(400, 999),
                'FlightDuration' => getDiffDate($timeGo, $endGo),
                'DeparturLocationCode' => $this->search['OriginLocationCodeGo'],
                'ArivelLocationCode' => $this->search['arivelLocationCodeGo'],
                'ResBookDesigCode' => 'A10',
                'ResBookDesigQuantity' => 10,
                // back
                'DepartureDateTimeBack' =>  $timeBack->toIso8601String(),
                'ArrivalDateTimeBack' => $endBack->toIso8601String(),
                'FlightNumberBack' => 'fake' . rand(400, 999),
                'FlightDurationBack' => getDiffDate($timeBack, $endBack),
                'DeparturLocationCodeBack' => $this->search['OriginLocationCodeBack'],
                'ArivelLocationCodeBack' => $this->search['arivelLocationCodeBack'],
                'tripType' => 'twoWay',
                'RPH' => 30566,
                'RPHBack' => 31423,
                'AirEquipType' => "737 - 700",
                'OperatingAirline' => 'IF',
                'ResBookDesigCodeBack' => 'A9',
                'ResBookDesigQuantityBack' => 9

            ];
        } else {
            $this->search = [
                'departure' => $data['OriginDestinationInformation']['DepartureDateTime'],
                'OriginLocationCode' => $data['OriginDestinationInformation']['OriginLocation']['@attributes']['LocationCode'],
                'arivelLocationCode' => $data['OriginDestinationInformation']['DestinationLocation']['@attributes']['LocationCode'],
                'curency' => $data['POS']['Source']['@attributes']['ISOCurrency'],
            ];
            $time = Carbon::create(randomDateTime(Carbon::create($this->search['departure'])->addMinutes(10 * 2)));
            $end = Carbon::parse($time)->addHours(2);
            $option = [
                'DepartureDateTime' =>  $time->toIso8601String(),
                'ArrivalDateTime' => $end->toIso8601String(),
                'FlightNumber' => 'fake' . rand(400, 999),
                'DirectionId' => 1,
                'FlightDuration' => getDiffDate($time, $end),
                'DeparturLocationCode' => $this->search['OriginLocationCode'],
                'ArivelLocationCode' => $this->search['arivelLocationCode'],
                'tripType' => 'oneWay',
                'RPH' => 30566,
                'AirEquipType' => "737 - 700",
                'OperatingAirline' => 'IF',
                'ResBookDesigCode' => 'A10',
                'ResBookDesigQuantity' => 10
            ];
        }

        foreach ($data['TravelerInfoSummary']['AirTravelerAvail']['PassengerTypeQuantity'] as $passenger) {
            if ($passenger['@attributes']['Code'] == 'ADT') {
                $this->search['adult'] = $passenger['@attributes']['Quantity'];
            }
            if ($passenger['@attributes']['Code'] == 'CHD') {
                $this->search['child'] = $passenger['@attributes']['Quantity'];
            }
            if ($passenger['@attributes']['Code'] == 'INF') {
                $this->search['infant'] = $passenger['@attributes']['Quantity'];
            }
        }

        $result = [
            'options' => $option, 'pricing' => $this->makePrice($this->search['adult'], $this->search['child'], $this->search['infant']),
            'passengers' => [
                'adult' => $this->search['adult'], 'child' => $this->search['child'], 'infant' => $this->search['infant']
            ]
        ];
        return response(view('Avtra.LowFareSearch.successResponse', ['result' => $result]), 200);
    }

    private function searchValidate($data)
    {
        // dd($data['TravelerInfoSummary']['AirTravelerAvail']['PassengerTypeQuantity']);
        if (empty($data['POS']['Source']['@attributes']['AirlineVendorID']) || $data['POS']['Source']['@attributes']['AirlineVendorID'] != "IF") {
            lugError('AirlineVendorID is empty or not equal IF', []);
            $code = 197;
            $error = "Error Occurred- please report";
            return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
        }
        if (empty($data['POS']['Source']['RequestorID']['@attributes']['ID']) || $data['POS']['Source']['RequestorID']['@attributes']['ID'] != "ALRAWDATAINOTA") {
            lugError('ID is empty or not equal ALRAWDATAINOTA', []);
            $code = 169;
            $error = "Agent code invalid";
            return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
        }
        if (empty($data['POS']['Source']['@attributes']['ISOCurrency']) || $data['POS']['Source']['@attributes']['ISOCurrency'] != "USD") {
            lugError('ISOCurrency is empty or not equal usd', []);
            $code = 61;
            $error = "Please provide a valid currency code";
            return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
        }

        if (isset($data['OriginDestinationInformation'][0])) {
            if (empty($data['OriginDestinationInformation'][0]['OriginLocation']['@attributes']['LocationCode']) || empty($data['OriginDestinationInformation'][0]['DestinationLocation']['@attributes']['LocationCode']) || empty($data['OriginDestinationInformation'][1]['OriginLocation']['@attributes']['LocationCode']) || empty($data['OriginDestinationInformation'][1]['DestinationLocation']['@attributes']['LocationCode'])) {
                lugError('AirlineVendorID is empty', []);
                $code = 509;
                $error = "Origin/Destination required";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }
            if (empty($data['OriginDestinationInformation'][0]['DepartureDateTime']) || empty($data['OriginDestinationInformation'][1]['DepartureDateTime'])) {
                lugError('DepartureDateTime is empty', []);
                $code = 158;
                $error = "Departure Date required";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }
            if ($data['OriginDestinationInformation'][0]['DepartureDateTime'] < Carbon::now()) {
                lugError('DepartureDateTime is smalller than now', []);
                $code = 531;
                $error = "No Flights or No Availability on date requested";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }
            if ($data['OriginDestinationInformation'][1]['DepartureDateTime'] < $data['OriginDestinationInformation'][0]['DepartureDateTime']) {
                lugError('DepartureDateTime is smalller than departure time back', []);
                $code = 320;
                $error = "Departure Date should be in increasing order";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }
        } else {
            if (empty($data['OriginDestinationInformation']['OriginLocation']['@attributes']['LocationCode']) || empty($data['OriginDestinationInformation']['DestinationLocation']['@attributes']['LocationCode'])) {
                lugError('AirlineVendorID is empty', []);
                $code = 509;
                $error = "Origin/Destination required";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }
            if (empty($data['OriginDestinationInformation']['DepartureDateTime'])) {
                lugError('DepartureDateTime is empty', []);
                $code = 158;
                $error = "Departure Date required";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }
            if ($data['OriginDestinationInformation']['DepartureDateTime'] < Carbon::now()) {
                lugError('DepartureDateTime is smalller than now', []);
                $code = 531;
                $error = "No Flights or No Availability on date requested";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }
            if (empty($data['TravelerInfoSummary']['AirTravelerAvail']['PassengerTypeQuantity'])) {
                lugError('PassengerTypeQuantity is empty', []);
                $code = 531;
                $error = "No Flights or No Availability on date requested";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }
        }
        if (empty($data['TravelerInfoSummary']['AirTravelerAvail']['PassengerTypeQuantity'])) {
            lugError('PassengerTypeQuantity is empty', []);
            $code = 531;
            $error = "No Flights or No Availability on date requested";
            return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
        }
    }

    protected function makePrice($adult, $child, $infant, $curency = "USD")
    {
        $adultBaseFare = 65.50;
        $infantFare = 15;
        $taxFare1 = 12;
        $taxFare2 = 12;
        $taxFare3 = 12;
        $taxFare4 = 12;
        $totalTax = $taxFare1 + $taxFare2 + $taxFare3 + $taxFare4;

        $pricing = [
            'adultBaseFare' => $adultBaseFare,
            'adultTotaleFare' => ($adultBaseFare + $totalTax),
            'totalbasefare' => ($adult * $adultBaseFare),
            'totalTotalefare' => ($adult * ($adultBaseFare + $totalTax)),
            'taxFare1' => $taxFare1,
            'taxFare2' => $taxFare2,
            'taxFare3' => $taxFare3,
            'taxFare4' => $taxFare4,
            'curency' => $this->search['curency'] ?? $curency
        ];

        if ($infant != 0) {
            $pricing['infantBaseFare'] = $infantFare;
            $pricing['infantTotalFare'] = $infantFare;
            $pricing['totalbasefare'] = $pricing['totalbasefare']  + ($infant * $infantFare);
            $pricing['totalTotalefare'] = $pricing['totalTotalefare'] + ($infant * ($infantFare));
        }

        if ($child != 0) {
            $pricing['childBaseFare'] = $adultBaseFare;
            $pricing['childTotaleFare'] = $adultBaseFare + $totalTax;
            $pricing['totalbasefare'] = $pricing['totalbasefare'] + ($child * $adultBaseFare);
            $pricing['totalTotalefare'] = $pricing['totalTotalefare'] + ($child * ($adultBaseFare + $totalTax));
        }
        return $pricing;
    }

    public function bookAndTicket()
    {
        extract(request()->all());
        $data = request()->all();

        $this->validateBook(request()->all());
        $OriginDestinationOption = [];
        if (isset($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0])) {

            $DepartureAirport = DB::table('airports')->where('abb', $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['DepartureAirport']['@attributes']['LocationCode'])->first();
            $ArrivalAirport = DB::table('airports')->where('abb', $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['ArrivalAirport']['@attributes']['LocationCode'])->first();
            $DepartureAirportBack = DB::table('airports')->where('abb', $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['DepartureAirport']['@attributes']['LocationCode'])->first();
            $ArrivalAirportBack = DB::table('airports')->where('abb', $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['ArrivalAirport']['@attributes']['LocationCode'])->first();

            $OriginDestinationOption = [
                'status' => 39, 'FlightNumber' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['@attributes']['FlightNumber'],
                'FareBasisCode' => 'A9OW', 'ResBookDesigCode' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['@attributes']['ResBookDesigCode'],
                'DepartureDateTime' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['@attributes']['DepartureDateTime'],
                'ArrivalDateTime' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['@attributes']['DepartureDateTime'],
                'RPH' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['@attributes']['RPH'],
                'OriginLocationCode' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['DepartureAirport']['@attributes']['LocationCode'],
                'ArivalLocationCode' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['ArrivalAirport']['@attributes']['LocationCode'],
                'OperatingAirlineCode' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['OperatingAirline']['@attributes']['Code'],
                'AirEquipType' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['Equipment']['@attributes']['AirEquipType'],
                'DepartureAirport' => $DepartureAirport->en,
                'ArrivalAirport' => $ArrivalAirport->en,
                // back
                'statusBack' => 39,
                'FlightNumberBack' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['@attributes']['FlightNumber'],
                'FareBasisCodeBack' => 'A9OW', 'ResBookDesigCode' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['@attributes']['ResBookDesigCode'],
                'DepartureDateTimeBack' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['@attributes']['DepartureDateTime'],
                'ArrivalDateTimeBack' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['@attributes']['DepartureDateTime'],
                'RPHBack' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['@attributes']['RPH'],
                'OriginLocationCodeBack' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['DepartureAirport']['@attributes']['LocationCode'],
                'ArivalLocationCodeBack' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['ArrivalAirport']['@attributes']['LocationCode'],
                'OperatingAirlineCodeBack' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['OperatingAirline']['@attributes']['Code'],
                'AirEquipTypeBack' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['Equipment']['@attributes']['AirEquipType'],
                'DepartureAirportBack' => $DepartureAirportBack->en,
                'ArrivalAirportBack' => $ArrivalAirportBack->en,
                'DirectionInd' => 'Return',
                'CreatedDateTme' => Carbon::now()->toIso8601String()
            ];
        } else {
            $DepartureAirport = DB::table('airports')->where('abb', $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['DepartureAirport']['@attributes']['LocationCode'])->first();
            $ArrivalAirport = DB::table('airports')->where('abb', $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['ArrivalAirport']['@attributes']['LocationCode'])->first();
            $OriginDestinationOption = [
                'status' => 39, 'FlightNumber' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['FlightNumber'],
                'FareBasisCode' => 'A9OW', 'ResBookDesigCode' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['ResBookDesigCode'],
                'DepartureDateTime' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['DepartureDateTime'],
                'ArrivalDateTime' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['DepartureDateTime'],
                'RPH' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['RPH'],
                'OriginLocationCode' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['DepartureAirport']['@attributes']['LocationCode'],
                'ArivalLocationCode' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['ArrivalAirport']['@attributes']['LocationCode'],
                'OperatingAirlineCode' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['OperatingAirline']['@attributes']['Code'],
                'AirEquipType' => $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['Equipment']['@attributes']['AirEquipType'],
                'DepartureAirport' => $DepartureAirport->en,
                'ArrivalAirport' => $ArrivalAirport->en,
                'DirectionInd' => 'OneWay',
                'CreatedDateTme' => Carbon::now()->toIso8601String()
            ];
        }
        if (!empty($data['TravelerInfo']['AirTraveler'][0])) {
            $adult = 0;
            $child = 0;
            $infant = 0;
            $adultInfoes = [];
            $childInfoes = [];
            $infantInfoes = [];

            foreach ($data['TravelerInfo']['AirTraveler'] as $passenger) {
                // dd($passenger);
                if ($passenger['@attributes']['PassengerTypeCode'] == 'ADT') {
                    $adult += 1;
                    $adultInfoes[] = ['nameInfo' => $passenger['PersonName'], 'docsInfo' => $passenger['Document']['@attributes'], 'generalInfo' => $passenger['@attributes']];
                }

                if ($passenger['@attributes']['PassengerTypeCode'] == 'CHD') {
                    $child += 1;
                    $childInfoes[] = ['nameInfo' => $passenger['PersonName'], 'docsInfo' => $passenger['Document']['@attributes'], 'generalInfo' => $passenger['@attributes']];
                }

                if ($passenger['@attributes']['PassengerTypeCode'] == 'INF') {
                    $infant += 1;
                    $infantInfoes[] = ['nameInfo' => $passenger['PersonName'], 'docsInfo' => $passenger['Document']['@attributes'], 'generalInfo' => $passenger['@attributes']];
                }
            }
        } else {
            $adult = 1;
            $passenger = $data['TravelerInfo']['AirTraveler'];
            $adultInfoes[] = ['nameInfo' => $passenger['PersonName'], 'docsInfo' => $passenger['Document']['@attributes'], 'generalInfo' => $passenger['@attributes']];
        }

        $passengers = ['adult' => $adultInfoes, 'child' => $childInfoes, 'infant' => $infantInfoes, 'count' => ['adult' => $adult, 'child' => $child, 'infant' => $infant]];
        $pricing = $this->makePrice($adult, $child, $infant);
        $countsTicket = $adult + $child + $infant;
        if (isset($data['Fulfillment']['PaymentDetails'])) {
            $tickets = [];
            for ($i = 0; $i < $countsTicket; $i++) {
                $tickets['tickets'][$i] = 'fakeTicket' . uniqid();
            }
            $tickets['idTicket'] = "fakeIssueTicket" . uniqid();
        } else {
            $idTicket = "fakeHoldTicket" . uniqid();
            $tickets = ['ticketLimit' => Carbon::now()->endOfDay()->toIso8601String(), 'idTicket' => $idTicket];
            $book = new FlyBaghdadBook();
            $book->pnr = $idTicket;
            $book->ticketLimit = Carbon::now()->endOfDay()->toIso8601String();
            $book->pass_info = $passengers;
            $book->fly_info = $OriginDestinationOption;
            $book->pricing = $pricing;
            $book->save();
        }
        $result = ['options' => $OriginDestinationOption, 'passengers' => $passengers, 'pricing' => $pricing, 'ticketsInfo' => $tickets];
        return response(view('Avtra.BookAndTicket.success2way', ['result' => $result]), 200);
    }

    private function validateBook($data)
    {
        if (empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0])) {

            if (empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['StopQuantity'])) {
                lugError('StopQuantityError is empty', []);
                return view('Avtra.BookAndTicket.StopQuantityError');
            }

            if (
                empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['OperatingAirline']['@attributes']['Code']) ||
                $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['OperatingAirline']['@attributes']['Code'] != "IF" ||
                empty($data['Fulfillment']['PaymentDetails']['PaymentDetail']['PaymentAmount']['@attributes']['Amount'])
            ) {
                lugError('DepartureDateTime is empty', []);
                $code = 197;
                $error = "Error Occurred- please report";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }

            if (empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['DepartureDateTime'])) {
                lugError('DepartureDateTime is empty', []);
                $code = 158;
                $error = "Departure Date required";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }
            if (empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['FlightNumber'])) {
                lugError('AirlineVendorID is empty or not equal IF', []);
                $code = 113;
                $error = "Mandatory details missing-Flight Number";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }

            if (
                empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['DepartureAirport']['@attributes']['LocationCode']) ||
                empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['ArrivalAirport']['@attributes']['LocationCode'])
            ) {
                lugError('LocationCode is empty or not equal IF', []);
                $code = 113;
                $error = "Mandatory booking details missing-Departure/Arrival Airports";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }
        } else {
            if (empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['@attributes']['StopQuantity']) || empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['@attributes']['StopQuantity'])) {
                lugError('StopQuantityError is empty', []);
                return view('Avtra.BookAndTicket.StopQuantityError');
            }

            if (
                empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['OperatingAirline']['@attributes']['Code']) ||
                $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['OperatingAirline']['@attributes']['Code'] != "IF" ||
                empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['OperatingAirline']['@attributes']['Code']) ||
                $data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['OperatingAirline']['@attributes']['Code'] != "IF" ||
                empty($data['Fulfillment']['PaymentDetails']['PaymentDetail']['PaymentAmount']['@attributes']['Amount'])
            ) {
                lugError('DepartureDateTime is empty', []);
                $code = 197;
                $error = "Error Occurred- please report";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }

            if (empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['@attributes']['DepartureDateTime']) || empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['@attributes']['DepartureDateTime'])) {
                lugError('DepartureDateTime is empty', []);
                $code = 158;
                $error = "Departure Date required";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }
            if (empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['@attributes']['FlightNumber']) || empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['@attributes']['FlightNumber'])) {
                lugError('AirlineVendorID is empty or not equal IF', []);
                $code = 113;
                $error = "Mandatory details missing-Flight Number";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }

            if (
                empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['DepartureAirport']['@attributes']['LocationCode']) ||
                empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['ArrivalAirport']['@attributes']['LocationCode']) ||
                empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['DepartureAirport']['@attributes']['LocationCode']) ||
                empty($data['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][1]['FlightSegment']['ArrivalAirport']['@attributes']['LocationCode'])
            ) {
                lugError('LocationCode is empty or not equal IF', []);
                $code = 113;
                $error = "Mandatory booking details missing-Departure/Arrival Airports";
                return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
            }
        }

        if (empty($data['TravelerInfo']['AirTraveler']['@attributes']['PassengerTypeCode'])) {
            lugError('PassengerTypeCode is empty ', []);
            $code = 26;
            $error = "At least one adult must be included";
            return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
        }

        if (empty($data['TravelerInfo']['AirTraveler']['@attributes']['TravelerNationality'])) {
            lugError('TravelerNationality is empty ', []);
            $code = 532;
            $error = "No Availability on requested date";
            return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
        }

        if (empty($data['TravelerInfo']['AirTraveler']['@attributes']['Gender'])) {
            lugError('Gender is empty ', []);
            $code = 320;
            $error = "Passenger Title is invalid";
            return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
        }

        if (empty($data['TravelerInfo']['Document']['@attributes']['DocID'])) {
            lugError('DocID is empty ', []);
            $code = 320;
            $error = "Invalid passport number";
            return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
        }

        if (empty($data['TravelerInfo']['Document']['@attributes']['DocHolderNationality'])) {
            lugError('DocHolderNationality is empty ', []);
            $code = 320;
            $error = "Invalid passenger nationality";
            return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
        }

        if (empty($data['TravelerInfo']['Document']['@attributes']['DocIssueCountry'])) {
            lugError('DocIssueCountry is empty ', []);
            $code = 181;
            $error = "Invalid Country Code";
            return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
        }

        if (empty($data['ContactPerson']['PersonName']['GivenName'])) {
            lugError('GivenName is empty ', []);
            $code = 285;
            $error = "Invalid first name";
            return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
        }

        if (empty($data['ContactPerson']['PersonName']['Surname'])) {
            lugError('GivenName is empty ', []);
            $code = 287;
            $error = "Invalid last name";
            return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
        }

        if (empty($data['ContactPerson']['Telephone']['@attributes']['PhoneNumber']) || empty($data['ContactPerson']['HomeTelephone']['@attributes']['PhoneNumber'])) {
            lugError('PhoneNumber is empty ', []);
            $code = 317;
            $error = "Invalid telephone number";
            return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
        }

        if (empty($data['ContactPerson']['Fulfillment']['PaymentDetails']['PaymentDetail']['@attributes']['PaymentType'])) {
            lugError('PhoneNumber is empty ', []);
            $code = 163;
            $error = "Please provide valid payment method";
            return view('Avtra.LowFareSearch.validateError', compact('error', 'code'));
        }
    }

    public function confirmBook()
    {
        extract(request()->all());
        $data = request()->all();
        $this->validateConfirm($data);
        $bookedItem = FlyBaghdadBook::where('pnr', $data['AirReservation']['BookingReferenceID']['@attributes']['ID'])->first();
        if (empty($bookedItem)) {
            lugError('bookedItem id is empty', []);
            $code = 91;
            $error = "Booking information mismatch - Itinerary mismatch";
            return view('Avtra.ConfirmBook.ErrorConfirm', compact('error', 'code'));
        }

        if ($bookedItem->ticketLimit < Carbon::now()->toIso8601String()) {
            lugError('bookedItem id is empty', []);
            $code = 91;
            $error = "Booking information mismatch - Itinerary mismatch";
            return view('Avtra.ConfirmBook.ErrorConfirm', compact('error', 'code'));
        }
        $countsTicket = $bookedItem->pass_info['count']['adult'] + $bookedItem->pass_info['count']['child'] + $bookedItem->pass_info['count']['infant'];
        for ($i = 0; $i < $countsTicket; $i++) {
            $tickets['tickets'][$i] = 'fakeTicket' . uniqid();
        }
        $tickets['idTicket'] = "fakeConfirmBook" . uniqid();

        $result = ['options' => $bookedItem->fly_info, 'passengers' => $bookedItem->pass_info, 'pricing' => $bookedItem->pricing, 'ticketsInfo' => $tickets];


        return response(view('Avtra.ConfirmBook.confirmBook', ['result' => $result]), 200);
    }
    private function validateConfirm($data)
    {


        if (empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0])) {
            if (empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['DepartureDateTime']) || empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['StopQuantity'])) {
                lugError('StopQuantityError is empty', []);
                return view('Avtra.BookAndTicket.StopQuantityError');
            }

            if (empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['DepartureDateTime']) || empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['ArrivalDateTime'])) {
                lugError('ArrivalDateTime or DepartureDateTime id is empty in oneway', []);
                $code = 197;
                $error = "Error Occurred- please report";
                return view('Avtra.ConfirmBook.ErrorConfirm', compact('error', 'code'));
            }

            if (
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['FlightNumber']) ||
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['Status'])
            ) {
                lugError('FlightSegment id is empty in oneway', []);
                $code = 91;
                $error = "Booking information mismatch - Itinerary mismatch";
                return view('Avtra.ConfirmBook.ErrorConfirm', compact('error', 'code'));
            }
            if (
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['RPH']) ||
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['DepartureAirport']['@attributes']['LocationCode']) ||
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['DepartureAirport']['@attributes']['LocationName']) ||
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['ArrivalAirport']['@attributes']['LocationName']) ||
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['ArrivalAirport']['@attributes']['LocationName']) ||
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['OperatingAirline']['@attributes']['Code']) ||
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['Equipment']['@attributes']['AirEquipType'])

            ) {
                $code = 89;
                $error = "Booking Status Mismatch";
                return view('Avtra.ConfirmBook.ErrorConfirm', compact('error', 'code'));
            }
        } else {
            if (empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['@attributes']['DepartureDateTime']) || empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['StopQuantity'])) {
                lugError('StopQuantityError is empty', []);
                return view('Avtra.BookAndTicket.StopQuantityError');
            }

            if (empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['@attributes']['DepartureDateTime']) || empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['ArrivalDateTime'])) {
                lugError('ArrivalDateTime or DepartureDateTime id is empty in oneway', []);
                $code = 197;
                $error = "Error Occurred- please report";
                return view('Avtra.ConfirmBook.ErrorConfirm', compact('error', 'code'));
            }

            if (
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['@attributes']['FlightNumber']) ||
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['@attributes']['Status'])
            ) {
                lugError('FlightSegment id is empty in oneway', []);
                $code = 91;
                $error = "Booking information mismatch - Itinerary mismatch";
                return view('Avtra.ConfirmBook.ErrorConfirm', compact('error', 'code'));
            }
            if (
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['@attributes']['RPH']) ||
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['DepartureAirport']['@attributes']['LocationCode']) ||
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['DepartureAirport']['@attributes']['LocationName']) ||
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['ArrivalAirport']['@attributes']['LocationName']) ||
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['ArrivalAirport']['@attributes']['LocationName']) ||
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['OperatingAirline']['@attributes']['Code']) ||
                empty($data['AirReservation']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['Equipment']['@attributes']['AirEquipType'])

            ) {
                $code = 89;
                $error = "Booking Status Mismatch";
                return view('Avtra.ConfirmBook.ErrorConfirm', compact('error', 'code'));
            }
        }

        if (empty($data['AirReservation']['BookingReferenceID']['@attributes']['ID'])) {
            lugError('BookingReferenceID id is empty', []);
            $code = 87;
            $error = "No matching bookings found";
            return view('Avtra.ConfirmBook.ErrorConfirm', compact('error', 'code'));
        }

        if (
            empty($data['AirReservation']['Ticketing']['@attributes']['TicketTimeLimit']) ||
            empty($data['AirReservation']['BookingReferenceID']['@attributes']['ID_Context'])
        ) {
            lugError('TicketTimeLimit is empty', []);
            $code = 89;
            $error = "Booking Status Mismatch";
            return view('Avtra.ConfirmBook.ErrorConfirm', compact('error', 'code'));
        }
    }
}
