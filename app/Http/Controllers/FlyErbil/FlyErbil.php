<?php

namespace App\Http\Controllers\FlyErbil;

use App\Http\Controllers\Controller;
use App\Models\FlyErbilAuthToken;
use App\Models\FlyErbilSearch;
use App\Models\FlyErbilToken;
use Carbon\Carbon;

class FlyErbil extends Controller
{
    protected $search = [];
    public function authorization()
    {

        extract(request()->all());
        lugWarning('getdata for authorization', [request()->all()]);

        if (empty($grant_type)) {
            return response([
                "error" => "invalid_request",
                "error_description" => "Missing form parameter: grant_type"
            ]);
        }

        if (empty($client_id)) {
            return response([
                "error" => "unauthorized_client",
                "error_description" => "UNKNOWN_CLIENT: Client was not identified by any client authenticator"
            ]);
        }

        if (empty($client_secret)) {
            return response([
                "error" => "unauthorized_client",
                "error_description" => "Client secret not provided in request"
            ]);
        }

        if (empty($username)) {
            return response([
                "error" => "invalid_request",
                "error_description" => "Missing parameter: username"
            ]);
        }

        if (empty($password)) {
            return response([
                "error" => "invalid_grant",
                "error_description" => "Invalid user credentials"
            ]);
        }

        $token = 'fakeAccess' . uniqId();
        $refreshToken = 'fakeRefresh' . uniqId();

        $auth = new FlyErbilAuthToken();
        $auth->access_token = $token;
        $auth->refresh_token = $refreshToken;
        $auth->save();
        lugInfo('end of authorize', [$auth,$token,$refreshToken]);
        return response([
            "access_token" => "$token",
            "expires_in" => 14400,
            "refresh_expires_in" => 28800,
            "refresh_token" => "$refreshToken",
            "token_type" => "bearer",
            "id_token" => "eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJ6eHBEZEluUi0xdWhvczl2UzV0TjZONWVHNzFaR2pkdWx5am84UUhXRGc0In0.eyJqdGkiOiIxNmQ2MmY3OC0wNTA2LTRjOGEtODdkYi1mNjZhYzAyY2ZiM2IiLCJleHAiOjE2Mzg5Njc0NjgsIm5iZiI6MCwiaWF0IjoxNjM4OTUzMDY4LCJpc3MiOiJodHRwczovL3Rlc3QtYXV0aC53b3JsZHRpY2tldC5uZXQvYXV0aC9yZWFsbXMvdGVzdC1za3l3b3JrLWdkcyIsImF1ZCI6InNtczQiLCJzdWIiOiJhYTJhMGIwYS00MWE3LTQ4YjgtOWIwYy02MDVkNTJjYzNlYzgiLCJ0eXAiOiJJRCIsImF6cCI6InNtczQiLCJhdXRoX3RpbWUiOjAsInNlc3Npb25fc3RhdGUiOiJjMTIyZmRlYS00OWZhLTQ0NmQtYjc3ZS02Mjk2M2RjZmI4ZWYiLCJhY3IiOiIxIiwib2ZmaWNlX2lkIjoiMjk0NzM2IiwiY29tcGFueV9pZCI6IjU1NjUxIiwidXNlcl9pZCI6Ijg0NTIyOTAiLCJuYW1lIjoieWFsYTEgSG9saWRheSIsInByZWZlcnJlZF91c2VybmFtZSI6InlhbGExIiwiZ2l2ZW5fbmFtZSI6InlhbGExIiwiZmFtaWx5X25hbWUiOiJIb2xpZGF5IiwiZW1haWwiOiJ5YWxhMUB3dC5jb20ifQ.Eq179zhj-q8i47IQOh81RcwxvbJXJBK1kF0FnHNJP1g40_0oPjYdFprwmqWbfEcmSQmjBSo6I0NkzwI_zLBrK5vOgocmfc_yUq0N22woDxsRmACK9iFreGV25ZaqLwddCHSgjg_yAFRLW0kfW9m27V6-wsvW31OXprwy1a1y5m_DAS8LGPsWtwAooLR2enGVgTOUkJJj_QkOzls9uDoR5s1mLX2sA3sDarXnyhKehT8bs2q0es9VBpYrKSC9OCaW3pTVAI9P4b_3ZozNgydbZ-qbqCtWACtM1yvfvbnWd_Ta50sgpDzJ_zqppFFmYsrMbgQDDGCxijw36Nhim8CVsg",
            "not-before-policy" => 1620197457,
            "session_state" => "c122fdea-49fa-446d-b77e-62963dcfb8ef"
        ], 200);
    }

    public function airLowFareSearch()
    {
        extract(request()->all());
        $header = request()->header();
        $accessToken = substr($header['authorization'][0], 7);
        $adult = 0;
        $child = 0;
        $infant = 0;
        if (isset($OriginDestinationInformation[0])) {
            $departureTimeGo = Carbon::create(randomDateTime(Carbon::create($OriginDestinationInformation[0]['DepartureDateTime'])));
            $departureTimeBack = Carbon::create(randomDateTime(Carbon::create($OriginDestinationInformation[1]['DepartureDateTime'])));
            $arivalTimeGo = Carbon::create(randomDateTime(Carbon::create($OriginDestinationInformation[0]['DepartureDateTime'])->addHours(2)));
            $arivalTimeBack = Carbon::create(randomDateTime(Carbon::create($OriginDestinationInformation[1]['DepartureDateTime'])->addHours(2)));

            $this->search = [
                'FlightNumberGO' => 'fake' . rand(9999, 7000),
                'ArrivalDateTimeGO' => $arivalTimeGo->toIso8601String(),
                'DepartureDateTimeGO' => $departureTimeGo->toIso8601String(),
                'OriginLocationGo' => $OriginDestinationInformation[0]['OriginLocation']['@attributes']['LocationCode'],
                'ArivalLocationGo' => $OriginDestinationInformation[0]['DestinationLocation']['@attributes']['LocationCode'],
                // back
                'FlightNumberBack' => 'fake' . rand(9999, 7000),
                'ArrivalDateTimeBack' => $arivalTimeBack->toIso8601String(),
                'DepartureDateTimeBack' => $departureTimeBack->toIso8601String(),
                'OriginLocationBack' => $OriginDestinationInformation[1]['OriginLocation']['@attributes']['LocationCode'],
                'ArivalLocationBack' => $OriginDestinationInformation[1]['DestinationLocation']['@attributes']['LocationCode'],
                'type' => 'twoway'

            ];
        } else {
            $departureTime = Carbon::create(randomDateTime(Carbon::create($OriginDestinationInformation['DepartureDateTime'])));
            $arivalTime = Carbon::create(randomDateTime(Carbon::create($OriginDestinationInformation['DepartureDateTime'])->addHours(2)));

            $this->search = [
                'FlightNumber' => rand(9999, 7000),
                'ArrivalDateTime' => $arivalTime->toIso8601String(),
                'DepartureDateTime' => $departureTime->toIso8601String(),
                'OriginLocation' => $OriginDestinationInformation['OriginLocation']['@attributes']['LocationCode'],
                'ArivalLocation' => $OriginDestinationInformation['DestinationLocation']['@attributes']['LocationCode'],
                'type' => 'oneway'
            ];
        }
        if (isset($TravelerInfoSummary['AirTravelerAvail'][0])) {
            foreach ($TravelerInfoSummary['AirTravelerAvail'] as $passenger) {
                if ($passenger['PassengerTypeQuantity']['@attributes']['Code'] == 'ADT') {
                    $adult = $passenger['PassengerTypeQuantity']['@attributes']['Quantity'];
                }
                if ($passenger['PassengerTypeQuantity']['@attributes']['Code'] == 'CHD') {
                    $child = $passenger['PassengerTypeQuantity']['@attributes']['Quantity'];
                }
                if ($passenger['PassengerTypeQuantity']['@attributes']['Code'] == 'INF') {
                    $infant = $passenger['PassengerTypeQuantity']['@attributes']['Quantity'];
                }
            }
        } else {
            $adult = $TravelerInfoSummary['AirTravelerAvail']['PassengerTypeQuantity']['@attributes']['Quantity'];
        }

        $token = FlyErbilAuthToken::where('access_token', $accessToken)->first();
        if (empty($token)) {
            return response(view('flyErbil.errorErbil'));
        }
        $this->search['adult'] = $adult;
        $this->search['child'] = $child;
        $this->search['infant'] = $infant;
        $result = ['searchInformations' => $this->search, 'pricing' => $this->makePrice()];
        $search = new FlyErbilSearch();
        $search->search_info = $result['searchInformations'];
        $search->price_info = $result['pricing'];
        $search->type = $this->search['type'];
        $search->access_token = $accessToken;
        $search->save();
        return response(view('flyErbil.searchsuccess', ['result' => $result]), 200);
    }

    private function makePrice()
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
            'totalTax' => $taxFare
        ];
        lugInfo('create pricing in search', [$pricing]);
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

        return $pricing;
    }

    public function getPrice()
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
        if (isset($TravelerInfoSummary['AirTravelerAvail'][0])) {
            $passengersCount = count($TravelerInfoSummary['AirTravelerAvail']);
            foreach ($TravelerInfoSummary['AirTravelerAvail'] as $passenger) {
                $passengers[] = $passenger;
            }
        } else {
            $passengersCount = 1;
            $passengers[] = $TravelerInfoSummary['AirTravelerAvail'];
        }
        return response(view('flyErbil.Price.successPrice', ['Flightinfo' => $Flightinfo, 'pricing' => $pricing, 'passengers' => $passengers]), 200);
    }
}
