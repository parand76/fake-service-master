<?php

namespace App\Http\Controllers\FlyErbil;

use App\Http\Controllers\Controller;
use App\Models\FlyErbilBook;
use App\Models\FlyErbilSearch;
use Carbon\Carbon;

class BookAndPeyment extends Controller
{
    public function airBook()
    {
        extract(request()->all());
        $header = request()->header();
        $accessToken = substr($header['authorization'][0], 7);
        $passengers = [];
        if (isset($AirItinerary['OriginDestinationOptions']['OriginDestinationOption'][0])) {
            $search = FlyErbilSearch::where('access_token', $accessToken)->whereIn('search_info->FlightNumberGO', [$AirItinerary['OriginDestinationOptions']['OriginDestinationOption'][0]['FlightSegment']['@attributes']['FlightNumber']])->first();
            if (empty($search)) {
                return response(view('flyErbil.errorErbil'));
            }
            $Flightinfo = $search->search_info;
            $pricing = $search->price_info;
        } else {
            $search = FlyErbilSearch::where('access_token', $accessToken)->whereIn('search_info->FlightNumber', [$AirItinerary['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['@attributes']['FlightNumber']])->where('access_token', $accessToken)->first();
            if (empty($search)) {
                return response(view('flyErbil.errorErbil'));
            }
            $Flightinfo = $search->search_info;
            $pricing = $search->price_info;
        }
        if (isset($TravelerInfo['AirTraveler'][0])) {
            $passengersCount = count($TravelerInfo['AirTraveler']);
            foreach ($TravelerInfo['AirTraveler'] as $passenger) {
                $passengers[] = $passenger;
            }
        } else {
            $passengersCount = 1;
            $passengers[] = $TravelerInfo['AirTraveler'];
        }

        $tickets = ['pnr' => 'fakeTicket' . uniqid(), 'timelimitTicket' => Carbon::today()->toIso8601String()];
        $book = new FlyErbilBook();
        $book->pnr_code = $tickets['pnr'];
        $book->timelimitTicket = $tickets['timelimitTicket'];
        $book->access_token = $accessToken;
        $book->price_info = $pricing;
        $book->passenger_info = $passengers;
        $book->save();
        return response(view('flyErbil.Book.successBook', ['Flightinfo' => $Flightinfo, 'pricing' => $pricing, 'passengers' => $passengers, 'tickets' => $tickets]), 200);
    }

    public function peyment()
    {
        extract(request()->all());
        $header = request()->header();
        $accessToken = substr($header['authorization'][0], 7);
        $book = FlyErbilBook::where('access_token', $accessToken)->where('pnr_code', $DemandTicketDetail['BookingReferenceID']['@attributes']['ID'])->first();
        $passengersInfo = $book->passenger_info;
        $pricing = $book->price_info;
        $tickets = [];
        foreach ($passengersInfo as $key => $info) {
            $tickets[$key] = 'fakeTicketpey' . uniqid();
        }
        return response(view('flyErbil.peyment.successPeyment', ['pnr' => $DemandTicketDetail['BookingReferenceID']['@attributes']['ID'], 'pricing' => $pricing, 'passengers' => $passengersInfo, 'tickets' => $tickets]), 200);
    }
}
