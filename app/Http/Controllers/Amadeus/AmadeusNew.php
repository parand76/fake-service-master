<?php

namespace App\Http\Controllers\Amadeus;

use App\Http\Controllers\Controller;
use App\Models\AmadeusNewFareRule;
use App\Models\AmadeusNewPnrRetrieve;
use App\Models\AmadeusNewPricePnr;
use App\Models\AmadeusNewSearch;
use App\Models\AmadeusNewSelectedFlight;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AmadeusNew extends Controller
{
    public function generateRandomString($length = 10) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function fareMasterPricerTravelBoardSearch()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            'add__MessageID' => 'required',
            'add__Action' => ['required', Rule::in('http://webservices.amadeus.com/FMPTBQ_21_4_1A')],
            'add__To' => ['required', Rule::in("https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA")],
            'oas__Security.oas__UsernameToken.oas__Username' => 'required',
            'oas__Security.oas__UsernameToken.oas__Nonce' => 'required',
            'oas__Security.oas__UsernameToken.oas__Password' => 'required',
            'oas__Security.oas__UsernameToken.oas1__Created' => 'required',
            'AMA_SecurityHostedUser.UserID' => 'required',
            'Fare_MasterPricerTravelBoardSearch.numberOfUnit.unitNumberDetail.0.numberOfUnits' => 'required',
            'Fare_MasterPricerTravelBoardSearch.numberOfUnit.unitNumberDetail.1.numberOfUnits' => 'required',
            'Fare_MasterPricerTravelBoardSearch.paxReference' => 'required',
            'Fare_MasterPricerTravelBoardSearch.fareOptions' => 'required',
            'Fare_MasterPricerTravelBoardSearch.travelFlightInfo.cabinId.cabin' => 'required',
            'Fare_MasterPricerTravelBoardSearch.itinerary' => 'required',
        ], [
            'add__MessageID.required' => '1.messageIdError',
            'add__Action.required' => '1.actionRequiredError',
            'add__Action.in' => '1.actionInError',
            'add__To.required' => '1.toRequiredError',
            'add__To.in' => '1.toInError',
            'oas__Security.oas__UsernameToken.oas__Username.required' => '1.userTokenError',
            'oas__Security.oas__UsernameToken.oas__Nonce.required' => '1.userTokenError',
            'oas__Security.oas__UsernameToken.oas__Password.required' => '1.userTokenError',
            'oas__Security.oas__UsernameToken.oas1__Created.required' => '1.userTokenError',
            'AMA_SecurityHostedUser.UserID.required' => 'actionInError',
            'Fare_MasterPricerTravelBoardSearch.numberOfUnit.unitNumberDetail.0.numberOfUnits.required' => '1.recNumberError',
            'Fare_MasterPricerTravelBoardSearch.numberOfUnit.unitNumberDetail.1.numberOfUnits.required' => '1.recNumberError',
            'Fare_MasterPricerTravelBoardSearch.paxReference.required' => '1.recNumberError',
            'Fare_MasterPricerTravelBoardSearch.fareOptions.required' => '1.recNumberError',
            'Fare_MasterPricerTravelBoardSearch.travelFlightInfo.cabinId.cabin.required' => '1.recNumberError',
            'Fare_MasterPricerTravelBoardSearch.itinerary.required' => '1.recNumberError',
        ]);

        ///> validation fails
        if ($validated->fails()) {
            return response(view('AmadeusNew.' . $validated->messages()->first()))->header('Content-Type', 'application/xml');
        }

        $number_of_rec = $Fare_MasterPricerTravelBoardSearch['numberOfUnit']['unitNumberDetail'][0]['numberOfUnits'];

        ///> get passengers counts
        $adt_count = 0;
        $chd_count = 0;
        $inf_count = 0;
        if (isset($Fare_MasterPricerTravelBoardSearch['paxReference'][0])) {
            foreach ($Fare_MasterPricerTravelBoardSearch['paxReference'] as $passenger) {
                if ($passenger['ptc'] == 'ADT') {
                    $adt_count = count($passenger['traveller']);
                }
                if ($passenger['ptc'] == 'CH') {
                    $chd_count = count($passenger['traveller']);
                }
                if ($passenger['ptc'] == 'INF') {
                    $inf_count = count($passenger['traveller']);
                }
            }
        } else {
            if ($Fare_MasterPricerTravelBoardSearch['paxReference']['ptc'] == 'ADT') {
                $adt_count = count($Fare_MasterPricerTravelBoardSearch['paxReference']['traveller']);
            }
            if ($Fare_MasterPricerTravelBoardSearch['paxReference']['ptc'] == 'CH') {
                $chd_count = count($Fare_MasterPricerTravelBoardSearch['paxReference']['traveller']);
            }
            if ($Fare_MasterPricerTravelBoardSearch['paxReference']['ptc'] == 'INF') {
                $inf_count = count($Fare_MasterPricerTravelBoardSearch['paxReference']['traveller']);
            }
        }

        ///> check adult count - one adutl should be exists
        if ($adt_count == 0) {
            return response(view('AmadeusNew.1.noFlightError'))->header('Content-Type', 'application/xml');
        }

        if (isset($Fare_MasterPricerTravelBoardSearch['itinerary'][0])) {
            ///> rounded trip
            $return_date = $Fare_MasterPricerTravelBoardSearch['itinerary'][1]['timeDetails']['firstDateTimeDetail']['date'];
            $departure_date = $Fare_MasterPricerTravelBoardSearch['itinerary'][0]['timeDetails']['firstDateTimeDetail']['date'];
            $origin_id = $Fare_MasterPricerTravelBoardSearch['itinerary'][0]['departureLocalization']['departurePoint']['locationId'];
            $destination_id = $Fare_MasterPricerTravelBoardSearch['itinerary'][0]['arrivalLocalization']['arrivalPointDetails']['locationId'];
        } else {
            ///> one way trip
            $return_date = false;
            $departure_date = $Fare_MasterPricerTravelBoardSearch['itinerary']['timeDetails']['firstDateTimeDetail']['date'];
            $origin_id = $Fare_MasterPricerTravelBoardSearch['itinerary']['departureLocalization']['departurePoint']['locationId'];
            $destination_id = $Fare_MasterPricerTravelBoardSearch['itinerary']['arrivalLocalization']['arrivalPointDetails']['locationId'];
        }

        ///> find flight
        $findFlight = AmadeusNewSearch::where('ADT', $adt_count)->where('CHD', $chd_count)->where('INF', $inf_count)->where('OriginLocation', $origin_id)->where('DestinationLocation', $destination_id)->where('DepratureDate', $departure_date)->where('ReturnDate', $return_date)->where('NumberOfRec', $number_of_rec)->first();
        if (is_null($findFlight)) {
            return response(view('AmadeusNew.1.noFlightError'))->header('Content-Type', 'application/xml');
        }

        ///> create session
        $SessionId = uniqid("fake_server_");
        $SessionToken = uniqid("fake_server_", true);
        AmadeusNewSelectedFlight::create([
            'FlightId' => $findFlight->id,
            'UserId' => $AMA_SecurityHostedUser['UserID']['@attributes']['PseudoCityCode'],
            'SessionId' => $SessionId,
            'SessionToken' => $SessionToken,
        ]);

        ///> response        
        return response('
        <?xml version="1.0" encoding="UTF-8"?>
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:awsse="http://xml.amadeus.com/2010/06/Session_v3" xmlns:wsa="http://www.w3.org/2005/08/addressing">
            <soap:Header>
                <wsa:To>http://www.w3.org/2005/08/addressing/anonymous</wsa:To>
                <wsa:From>
                    <wsa:Address>https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA</wsa:Address>
                </wsa:From>
                <wsa:Action>http://webservices.amadeus.com/PNRRET_21_1_1A</wsa:Action>
                <wsa:MessageID>urn:uuid:4c54fcaf-61b2-8ba4-5d10-3963c531a0e7</wsa:MessageID>
                <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">56c81f14-3a75-4cd8-b616-838bd685b366</wsa:RelatesTo>
                <awsse:Session TransactionStatusCode="InSeries">
                    <awsse:SessionId>' . $SessionId . '</awsse:SessionId>
                    <awsse:SequenceNumber>1</awsse:SequenceNumber>
                    <awsse:SecurityToken>' . $SessionToken . '</awsse:SecurityToken>
                </awsse:Session>
            </soap:Header>
        
            ' . $findFlight->Response_XML . '
        
        </soap:Envelope>
        ')->header('Content-Type', 'application/xml');
    }

    public function fareInformativePricingWithoutPNR()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            'add__MessageID' => 'required',
            'add__Action' => ['required', Rule::in('http://webservices.amadeus.com/TIPNRQ_18_1_1A')],
            'add__To' => ['required', Rule::in("https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA")],
            'oas__Security.oas__UsernameToken.oas__Username' => 'required',
            'oas__Security.oas__UsernameToken.oas__Nonce' => 'required',
            'oas__Security.oas__UsernameToken.oas__Password' => 'required',
            'oas__Security.oas__UsernameToken.oas1__Created' => 'required',
            'awsse__Session.@attributes.TransactionStatusCode' => ['required', Rule::in(['Start'])],
            'AMA_SecurityHostedUser.UserID.@attributes.PseudoCityCode' => 'required',
            'Fare_InformativePricingWithoutPNR' => 'required',
            // 'Fare_InformativePricingWithoutPNR.passengersGroup' => 'required',
            // 'Fare_InformativePricingWithoutPNR.segmentGroup' => 'required',
        ], [
            'add__MessageID.required' => '1.messageIdError',
            'add__Action.required' => '1.actionRequiredError',
            'add__Action.in' => '1.actionInError',
            'add__To.required' => '1.toRequiredError',
            'add__To.in' => '1.toInError',
            'oas__Security.oas__UsernameToken.oas__Username.required' => '1.userTokenError',
            'oas__Security.oas__UsernameToken.oas__Nonce.required' => '1.userTokenError',
            'oas__Security.oas__UsernameToken.oas__Password.required' => '1.userTokenError',
            'oas__Security.oas__UsernameToken.oas1__Created.required' => '1.userTokenError',
            'awsse__Session.@attributes.TransactionStatusCode.required' => '1.recNumberError',
            'awsse__Session.@attributes.TransactionStatusCode.in' => '1.recNumberError',
            'AMA_SecurityHostedUser.UserID.@attributes.PseudoCityCode.required' => '1.actionInError',
            'Fare_InformativePricingWithoutPNR.required' => '1.recNumberError',
            // 'Fare_InformativePricingWithoutPNR.passengersGroup.required' => '1.recNumberError',
            // 'Fare_InformativePricingWithoutPNR.segmentGroup.required' => '1.recNumberError',
        ]);

        ///> validation fails
        if ($validated->fails()) {
            return response(view('AmadeusNew.' . $validated->messages()->first()))->header('Content-Type','application/xml');
        }

        ///> check flight
        $selectedFlight = AmadeusNewSelectedFlight::where('UserId', $AMA_SecurityHostedUser['UserID']['@attributes']['PseudoCityCode'])->orderByDesc('created_at')->first();
        if (is_null($selectedFlight)) {
            return response(view('AmadeusNew.noFlightError'))->header('Content-Type','application/xml');
        }

        ///> session info
        $SessionId = $selectedFlight->SessionId;
        $SessionNumber = $selectedFlight->SessionNumber;
        $SessionToken = $selectedFlight->SessionToken;

        ///> response
        return response(view('AmadeusNew.2.success', compact('SessionId', 'SessionNumber', 'SessionToken')))->header('Content-Type', 'application/xml');
    }

    public function miniRuleGetFromRec()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            'add__MessageID' => 'required',
            'add__Action' => ['required', Rule::in('http://webservices.amadeus.com/TMRXRQ_18_1_1A')],
            'add__To' => ['required', Rule::in("https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA")],
            'awsse__Session.@attributes.TransactionStatusCode' => ['required', Rule::in(['InSeries'])],
            'awsse__Session.awsse__SessionId' => 'required',
            'awsse__Session.awsse__SequenceNumber' => 'required',
            'awsse__Session.awsse__SecurityToken' => 'required',
            'MiniRule_GetFromRec.groupRecords' => 'required',
        ], [
            'add__MessageID.required' => 'MessageID',
            'add__Action.required' => 'Action',
            'add__Action.in' => 'Action',
            'add__To.required' => 'To',
            'add__To.in' => 'To',
            'awsse__Session.@attributes.TransactionStatusCode.required' => 'Session',
            'awsse__Session.@attributes.TransactionStatusCode.in' => 'Session',
            'awsse__Session.awsse__SessionId.required' => 'SessionId',
            'awsse__Session.awsse__SequenceNumber.required' => 'SequenceNumber',
            'awsse__Session.awsse__SecurityToken.required' => 'SecurityToken',
            'MiniRule_GetFromRec.groupRecords.required' => 'groupRecords',
        ]);

        ///> validation fails
        if ($validated->fails()) {
            $message = $validated->messages()->first();
            return response(view('AmadeusNew.3.error', compact('message')))->header('Content-Type','application/xml');
        }

        ///> check session
        $selectedFlight = AmadeusNewSelectedFlight::where('SessionId', $awsse__Session['awsse__SessionId'])->orderByDesc('created_at')->first();
        if (is_null($selectedFlight)) {
            $message = 'SESSION NOT FOUND';
            return response(view('AmadeusNew.3.error', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> session info
        $SessionId = $selectedFlight->SessionId;
        $SessionToken = $selectedFlight->SessionToken;

        $RefrenceType = $MiniRule_GetFromRec['groupRecords']['recordID']['referenceType'];
        $UniqueRefrence = $MiniRule_GetFromRec['groupRecords']['recordID']['uniqueReference'];

        ///> get flight info
        $flightInfo = AmadeusNewSearch::where('id', $selectedFlight->FlightId)->first();
        $PassngersCount = $flightInfo['ADT'] + $flightInfo['CHD'] + $flightInfo['INF'];
        $OriginId = $flightInfo['OriginLocation'];
        $DestinationId = $flightInfo['DestinationLocation'];
        $Return = ($flightInfo['ReturnDate'] == "false") ? 'false' : 'true';

        ///> find fare rules
        $FareRules = AmadeusNewFareRule::where('RefrenceType', $RefrenceType)
            ->where('UniqueRefrence', $UniqueRefrence)
            ->where('Origin', $OriginId)
            ->where('Destination', $DestinationId)
            ->where('HasReturn', $Return)
            ->where('PassengersCount', 'LIKE', $PassngersCount)
            ->first();
        if (is_null($FareRules)) {
            $message = 'NO FARE RULES FOUND';
            return response(view('AmadeusNew.3.error', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> response
        return response('
        <?xml version="1.0" encoding="UTF-8"?>
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:awsse="http://xml.amadeus.com/2010/06/Session_v3" xmlns:wsa="http://www.w3.org/2005/08/addressing">
            <soap:Header>
                <wsa:To>http://www.w3.org/2005/08/addressing/anonymous</wsa:To>
                <wsa:From>
                    <wsa:Address>https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA</wsa:Address>
                </wsa:From>
                <wsa:Action>http://webservices.amadeus.com/PNRRET_21_1_1A</wsa:Action>
                <wsa:MessageID>urn:uuid:4c54fcaf-61b2-8ba4-5d10-3963c531a0e7</wsa:MessageID>
                <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">56c81f14-3a75-4cd8-b616-838bd685b366</wsa:RelatesTo>
                <awsse:Session TransactionStatusCode="InSeries">
                    <awsse:SessionId>' . $SessionId . '</awsse:SessionId>
                    <awsse:SequenceNumber>2</awsse:SequenceNumber>
                    <awsse:SecurityToken>' . $SessionToken . '</awsse:SecurityToken>
                </awsse:Session>
            </soap:Header>
        
            ' . $FareRules->Response_XML . '
        
        </soap:Envelope>
        ')->header('Content-Type', 'application/xml');
    }

    public function securitySignOut()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            'awsse__Session' => 'required',
            'awsse__Session.@attributes.TransactionStatusCode' => ['required', Rule::in(['End', 'end'])],
            'awsse__Session.awsse__SessionId' => 'required',
            'awsse__Session.awsse__SequenceNumber' => 'required',
            'awsse__Session.awsse__SecurityToken' => 'required',
            'add__MessageID' => 'required',
            'add__Action' => ['required', Rule::in('http://webservices.amadeus.com/VLSSOQ_04_1_1A')],
            'add__To' => ['required', Rule::in("https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA")],
        ], [
            'awsse__Session.required' => 'Session',
            'awsse__Session.@attributes.TransactionStatusCode.required' => 'TransactionStatusCode',
            'awsse__Session.@attributes.TransactionStatusCode.in' => 'TransactionStatusCode',
            'awsse__Session.awsse__SessionId.required' => 'SessionId',
            'awsse__Session.awsse__SequenceNumber.required' => 'SequenceNumber',
            'awsse__Session.awsse__SecurityToken.required' => 'SecurityToken',
            'add__MessageID.required' => 'MessageID',
            'add__Action.required' => 'Action',
            'add__Action.in' => 'Action',
            'add__To.required' => 'To',
            'add__To.in' => 'add__To',
        ]);

        ///> if validation fails
        if ($validated->fails()) {
            $message = $validated->messages()->first();
            return response(view("AmadeusNew.4.error", compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> check  session
        $selectedFlight = AmadeusNewSelectedFlight::where('SessionId', $awsse__Session['awsse__SessionId'])->orderByDesc('created_at')->first();
        if (is_null($selectedFlight)) {
            $message = 'SESSION NOT FOUND';
            return response(view('AmadeusNew.4.error', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> session info
        $SessionId = $selectedFlight->SessionId;
        $SessionToken = $selectedFlight->SessionToken;

        ///>signout session
        $signOut = AmadeusNewSelectedFlight::find($selectedFlight->id);
        $signOut->delete();

        ///> response
        return response(view('AmadeusNew.4.success', compact('SessionId', 'SessionToken')))->header('Content-Type', 'application/xml');
    }

    public function airSellFromRecommendation()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            'add__MessageID' => 'required',
            'add__Action' => 'required',
            'add__To' => 'required',
            'oas__Security' => 'required',
            'oas__Security.oas__UsernameToken.oas__Username' => 'required',
            'oas__Security.oas__UsernameToken.oas__Nonce' => 'required',
            'oas__Security.oas__UsernameToken.oas__Password' => 'required',
            'oas__Security.oas__UsernameToken.oas1__Created' => 'required',
            'AMA_SecurityHostedUser.UserID.@attributes.PseudoCityCode' => 'required',
            'awsse__Session.@attributes.TransactionStatusCode' => ['required', Rule::in('Start', 'start')],
            'Air_SellFromRecommendation.itineraryDetails' => 'required',
        ], [
            'add__MessageID.required' => 'MessageID',
            'add__Action.required' => 'AddAction',
            'add__To.required' => 'AddTo',
            'oas__Security.required' => 'Security',
            'oas__Security.oas__UsernameToken.oas__Username.required' => 'Session',
            'oas__Security.oas__UsernameToken.oas__Nonce.required' => 'Session',
            'oas__Security.oas__UsernameToken.oas__Password.required' => 'Session',
            'oas__Security.oas__UsernameToken.oas1__Created.required' => 'Session',
            'AMA_SecurityHostedUser.UserID.@attributes.PseudoCityCode.required' => 'UserID',
            'awsse__Session.@attributes.TransactionStatusCode.required' => 'TransactionStatusCode',
            'awsse__Session.@attributes.TransactionStatusCode.in' => 'TransactionStatusCode',
            'Air_SellFromRecommendation.itineraryDetails.required' => 'itineraryDetails',
        ]);

        ///> if validation fails
        if ($validated->fails()) {
            $message = $validated->messages()->first();
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> check session
        $selectedFlight = AmadeusNewSelectedFlight::where('UserId', $AMA_SecurityHostedUser['UserID']['@attributes']['PseudoCityCode'])->orderBy('id', 'desc')->first();
        if (is_null($selectedFlight)) {
            $message = 'SESSION NOT FOUND';
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> session info
        $SessionId = $selectedFlight->SessionId;
        $SessionToken = $selectedFlight->SessionToken;

        ///> response
        return response(view('AmadeusNew.5.success', compact('SessionId', 'SessionToken')))->header('Content-Type', 'application/xml');
    }

    public function PNRAddMultiElements()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            'awsse__Session.@attributes.TransactionStatusCode' => ['required', Rule::in('InSeries')],
            'awsse__Session.awsse__SessionId' => 'required',
            'awsse__Session.awsse__SequenceNumber' => 'required',
            'awsse__Session.awsse__SecurityToken' => 'required',
            'add__MessageID' => 'required',
            'add__Action' => 'required',
            'add__To' => 'required',
            'PNR_AddMultiElements' => 'required'
        ], [
            'awsse__Session.@attributes.TransactionStatusCode.required' => 'TransactionStatusCode',
            'awsse__Session.@attributes.TransactionStatusCode.in' => 'TransactionStatusCode',
            'awsse__Session.awsse__SessionId.required' => 'SessionId',
            'awsse__Session.awsse__SequenceNumber.required' => 'SequenceNumber',
            'awsse__Session.awsse__SecurityToken.required' => 'SecurityToken',
            'add__MessageID.required' => 'MessageID',
            'add__Action.required' => 'Action',
            'add__To.required' => 'To',
            'PNR_AddMultiElements.required' => 'AddMultiElements',
        ]);

        ///> validation fails
        if ($validated->fails()) {
            $message = $validated->messages()->first();
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> check session
        $selectedFlight = AmadeusNewSelectedFlight::where('SessionId', $awsse__Session['awsse__SessionId'])->first();
        if (is_null($selectedFlight)) {
            $message = 'SESSION NOT FOUND';
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> session info
        $SessionId = $selectedFlight->SessionId;
        $SessionToken = $selectedFlight->SessionToken;
        $SessionSequenceNumber = $awsse__Session['awsse__SequenceNumber'];

        ///> get flight info
        $flightInfo = AmadeusNewSearch::where('id', $selectedFlight->FlightId)->first();
        $PassngersCount = $flightInfo['ADT'] + $flightInfo['CHD'] + $flightInfo['INF'];

        if (isset($PNR_AddMultiElements['travellerInfo'])) {
            ///> turn number 1 / 6-PNR_AddMultiElements

            if (isset($PNR_AddMultiElements['travellerInfo'][0])) {
                if (count($PNR_AddMultiElements['travellerInfo'][0]['passengerData']) == 1) {

                    $AdultInfantCount = 0;
                } else {

                    $AdultInfantCount = count($PNR_AddMultiElements['travellerInfo'][0]['passengerData']);
                }
            } else {

                $AdultInfantCount = count($PNR_AddMultiElements['travellerInfo']['passengerData']);
            }

            $TravelersCount = count($PNR_AddMultiElements['travellerInfo']) + $AdultInfantCount;
            $TravellersInfo = $PNR_AddMultiElements['travellerInfo'];

            ///> check counts
            if ($PassngersCount != $TravelersCount) {
                $message = 'PASSENGERS COUNT DISMATH';
                return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
            }

            $ControlNumber = null;
            $path = 'AmadeusNew.6.success1';

        } else {

            ///> turn number 2 / 10-PNR_AddMultiElements {2nd time} 
            $TravellersInfo = null;
            $ControlNumber = $this->generateRandomString(6);
            $newRecord =  new AmadeusNewPnrRetrieve();
            $newRecord->CompanyId = '1A';
            $newRecord->ControlNumber = $ControlNumber;
            $newRecord->ADT = $flightInfo['ADT'];
            $newRecord->CHD = $flightInfo['CHD'];
            $newRecord->INF = $flightInfo['INF'];
            $newRecord->Origin = $flightInfo['OriginLocation'];
            $newRecord->Destination = $flightInfo['DestinationLocation'];
            $newRecord->DepratureDate = $flightInfo['DepratureDate'];
            $newRecord->ReturnDate = $flightInfo['ReturnDate'];
            $newRecord->save();
            $path = 'AmadeusNew.6.success2';
        }

        ///> response
        return response(view($path, compact('SessionId', 'SessionToken', 'SessionSequenceNumber', 'TravellersInfo', 'ControlNumber')))->header('Content-Type', 'application/xml');
    }

    public function createFormOfPayment()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            'awsse__Session.@attributes.TransactionStatusCode' => ['required', Rule::in('InSeries')],
            'awsse__Session.awsse__SessionId' => 'required',
            'awsse__Session.awsse__SequenceNumber' => 'required',
            'awsse__Session.awsse__SecurityToken' => 'required',
            'add__MessageID' => 'required',
            'add__Action' => 'required',
            'add__To' => 'required',
            'FOP_CreateFormOfPayment.transactionContext.transactionDetails.code' => ['required', Rule::in('FP')],
            'FOP_CreateFormOfPayment.fopGroup.mopDescription.mopDetails.fopPNRDetails.fopDetails.fopCode' => ['required', Rule::in('CASH')],
        ], [
            'awsse__Session.@attributes.TransactionStatusCode.required' => 'TransactionStatusCode',
            'awsse__Session.@attributes.TransactionStatusCode.in' => 'TransactionStatusCode',
            'awsse__Session.awsse__SessionId.required' => 'Session',
            'awsse__Session.awsse__SequenceNumber.required' => 'Session',
            'awsse__Session.awsse__SecurityToken.required' => 'Session',
            'add__MessageID.required' => 'MessageID',
            'add__Action.required' => 'Action',
            'add__To.required' => 'To',
            'FOP_CreateFormOfPayment.transactionContext.transactionDetails.code.required' => 'transactionDetails',
            'FOP_CreateFormOfPayment.transactionContext.transactionDetails.code.in' => 'transactionDetails',
            'FOP_CreateFormOfPayment.fopGroup.mopDescription.mopDetails.fopPNRDetails.fopDetails.fopCode.required' => 'fopDetails',
            'FOP_CreateFormOfPayment.fopGroup.mopDescription.mopDetails.fopPNRDetails.fopDetails.fopCode.in' => 'fopDetails',
        ]);

        ///> validation fails
        if ($validated->fails()) {
            $message = $validated->messages()->first();
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> check session
        $selectedFlight = AmadeusNewSelectedFlight::where('SessionId', $awsse__Session['awsse__SessionId'])->first();
        if (is_null($selectedFlight) || $selectedFlight->expire_at > Carbon::now()) {
            $message = 'SESSION NOT FOUND';
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> session info
        $SessionId = $selectedFlight->SessionId;
        $SessionToken = $selectedFlight->SessionToken;

        ///> response
        return response(view('AmadeusNew.7.success', compact('SessionId', 'SessionToken')))->header('Content-Type', 'application/xml');
    }

    public function farePricePNRWithBookingClass()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            'awsse__Session.@attributes.TransactionStatusCode' => ['required', Rule::in('InSeries')],
            'awsse__Session.awsse__SessionId' => 'required',
            'awsse__Session.awsse__SequenceNumber' => 'required',
            'awsse__Session.awsse__SecurityToken' => 'required',
            'add__MessageID' => 'required',
            'add__Action' => 'required',
            'add__To' => 'required',
            'Fare_PricePNRWithBookingClass.pricingOptionGroup' => 'required',
        ], [
            'awsse__Session.@attributes.TransactionStatusCode.required' => 'awsse__Session.@attributes.TransactionStatusCode',
            'awsse__Session.@attributes.TransactionStatusCode.in' => 'awsse__Session.@attributes.TransactionStatusCode',
            'awsse__Session.awsse__SessionId.required' => 'Session',
            'awsse__Session.awsse__SequenceNumber.required' => 'Session',
            'awsse__Session.awsse__SecurityToken.required' => 'Session',
            'add__MessageID.required' => 'MessageID',
            'add__Action.required' => 'Action',
            'add__To.required' => 'To',
            'Fare_PricePNRWithBookingClass.pricingOptionGroup.required' => 'pricingOptionGroup',
        ]);

        ///> request fails
        if ($validated->fails()) {
            $message = $validated->messages()->first();
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> check session
        $selectedFlight = AmadeusNewSelectedFlight::where('SessionId', $awsse__Session['awsse__SessionId'])->orderByDesc('id')->first();
        if (is_null($selectedFlight)) {
            $message = 'SESSION NOT FOUND';
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> session info
        $SessionId = $selectedFlight->SessionId;
        $SessionToken = $selectedFlight->SessionToken;

        ///> get flight info
        $flightInfo = AmadeusNewSearch::where('id', $selectedFlight->FlightId)->first();
        $ADT = $flightInfo->ADT;
        $CHD = $flightInfo->CHD;
        $INF = $flightInfo->INF;
        $Origin = $flightInfo->OriginLocation;
        $Destination = $flightInfo->DestinationLocation;
        $DepratureDate = $flightInfo->DepratureDate;
        $ReturnDate = $flightInfo->ReturnDate;

        $priceInfo = AmadeusNewPricePnr::where('ADT', $ADT)
            ->where('CHD', $CHD)
            ->where('INF', $INF)
            ->where('Origin', $Origin)
            ->where('Destination', $Destination)
            ->where('DepratureDate', $DepratureDate)
            ->where('ReturnDate', $ReturnDate)
            ->first();

        if (is_null($priceInfo)) {
            $message = 'PRICE INFO NOT FOUND';
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> response
        return response($priceInfo->Response_XML)->header('Content-Type', 'application/xml');
    }

    public function ticketCreateTSTFromPricing()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            'awsse__Session.@attributes.TransactionStatusCode' => ['required', Rule::in('InSeries')],
            'awsse__Session.awsse__SessionId' => 'required',
            'awsse__Session.awsse__SecurityToken' => 'required',
            'awsse__Session.awsse__SequenceNumber' => 'required',
            'add__MessageID' => 'required',
            'add__Action' => 'required',
            'add__To' => 'required',
            'Ticket_CreateTSTFromPricing.psaList' => 'required',
        ], [
            'awsse__Session.@attributes.TransactionStatusCode.required' => 'TransactionStatusCode',
            'awsse__Session.@attributes.TransactionStatusCode.in' => 'TransactionStatusCode',
            'awsse__Session.awsse__SessionId.required' => 'Session',
            'awsse__Session.awsse__SecurityToken.required' => 'Session',
            'awsse__Session.awsse__SequenceNumber.required' => 'Session',
            'add__MessageID.required' => 'MessageID',
            'add__Action.required' => 'Action',
            'add__To.required' => 'To',
            'Ticket_CreateTSTFromPricing.psaList.required' => 'psaList',
        ]);

        ///> request validation fails
        if ($validated->fails()) {
            $message = $validated->messages()->first();
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> check session
        $selectedFlight = AmadeusNewSelectedFlight::where('SessionId', $awsse__Session['awsse__SessionId'])->orderByDesc('id')->first();
        if (is_null($selectedFlight)) {
            $message = 'SESSION NOT FOUND';
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> session info
        $SessionId = $selectedFlight->SessionId;
        $SessionToken = $selectedFlight->SessionToken;
        $SessionSequenceNumber = $awsse__Session['awsse__SequenceNumber'];

        ///> response
        return response(view('AmadeusNew.9.success', compact('SessionId', 'SessionToken', 'SessionSequenceNumber')))->header('Content-Type', 'application/xml');
    }

    public function PNRRetrieve()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            'add__MessageID' => 'required',
            'add__Action' => 'required',
            'add__To' => 'required',
            'oas__Security.oas__UsernameToken.oas__Username' => 'required',
            'oas__Security.oas__UsernameToken.oas__Nonce' => 'required',
            'oas__Security.oas__UsernameToken.oas__Password' => 'required',
            'oas__Security.oas__UsernameToken.oas1__Created' => 'required',
            'AMA_SecurityHostedUser.UserID.@attributes.PseudoCityCode' => 'required',
            'PNR_Retrieve.retrievalFacts.retrieve' => 'required',
            'PNR_Retrieve.retrievalFacts.reservationOrProfileIdentifier.reservation.companyId' => 'required',
            'PNR_Retrieve.retrievalFacts.reservationOrProfileIdentifier.reservation.controlNumber' => 'required',
        ], [
            'add__MessageID.required' => 'MessageID',
            'add__Action.required' => 'Action',
            'add__To.required' => 'Action',
            'oas__Security.oas__UsernameToken.oas__Username.required' => 'Username',
            'oas__Security.oas__UsernameToken.oas__Nonce.required' => 'Nonce',
            'oas__Security.oas__UsernameToken.oas__Password.required' => 'Password',
            'oas__Security.oas__UsernameToken.oas1__Created.required' => 'Created',
            'AMA_SecurityHostedUser.UserID.@attributes.PseudoCityCode.required' => 'UserID',
            'PNR_Retrieve.retrievalFacts.retrieve.required' => 'retrievalFacts',
            'PNR_Retrieve.retrievalFacts.reservationOrProfileIdentifier.reservation.companyId.required' => 'companyId',
            'PNR_Retrieve.retrievalFacts.reservationOrProfileIdentifier.reservation.controlNumber.required' => 'controlNumber',
        ]);

        ///> request validation fails
        if ($validated->fails()) {
            $message = $validated->messages()->first();
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> find info with company id
        $PnrRetrieve = AmadeusNewPnrRetrieve::where('CompanyId',$PNR_Retrieve['retrievalFacts']['reservationOrProfileIdentifier']['reservation']['companyId'])
            ->where('ControlNumber',$PNR_Retrieve['retrievalFacts']['reservationOrProfileIdentifier']['reservation']['controlNumber'])
            ->first();
        if (is_null($PnrRetrieve)) {
            $message = 'PNR RETRIEVE NOT FOUND';
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }

        $ControlNumber = $PnrRetrieve->ControlNumber;
        $ADT = $PnrRetrieve->ADT;
        $CHD = $PnrRetrieve->CHD;
        $INF = $PnrRetrieve->INF;
        $Origin = $PnrRetrieve->Origin;
        $Destination = $PnrRetrieve->Destination;
        $DepratureDate = $PnrRetrieve->DepratureDate;
        $ReturnDate = $PnrRetrieve->ReturnDate;

        ///> find flight
        $selectedFlight = AmadeusNewSearch::where('ADT', $ADT)
            ->where('CHD', $CHD)
            ->where('INF', $INF)
            ->where('OriginLocation', $Origin)
            ->where('DestinationLocation', $Destination)
            ->where('DepratureDate', $DepratureDate)
            ->where('ReturnDate', $ReturnDate)
            ->first();
        if (is_null($selectedFlight)) {
            $message = 'FLIGHT NOT FOUND';
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }
        
        ///> session
        $SessionId = uniqid("fake_server_");
        $SessionToken = uniqid("fake_server_", true);
        $newRecord = new AmadeusNewSelectedFlight();
        $newRecord->FlightId = $selectedFlight->id;
        $newRecord->PNRId = $PnrRetrieve->id;
        $newRecord->UserId = $AMA_SecurityHostedUser['UserID']['@attributes']['PseudoCityCode'];
        $newRecord->SessionId = $SessionId;
        $newRecord->SessionToken = $SessionToken;
        $newRecord->save();

        ///> response
        if(is_null($PnrRetrieve->Response_XML)) {
            return response(view('AmadeusNew.12.success',compact('SessionId','SessionToken','ControlNumber','ADT','CHD','INF')))->header('Content-Type', 'application/xml');
        } else {
            return response('
            <?xml version="1.0" encoding="UTF-8"?>
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:awsse="http://xml.amadeus.com/2010/06/Session_v3" xmlns:wsa="http://www.w3.org/2005/08/addressing">
                <soap:Header>
                    <wsa:To>http://www.w3.org/2005/08/addressing/anonymous</wsa:To>
                    <wsa:From>
                        <wsa:Address>https://noded3.test.webservices.amadeus.com/1ASIWFLY7FA</wsa:Address>
                    </wsa:From>
                    <wsa:Action>http://webservices.amadeus.com/PNRRET_21_1_1A</wsa:Action>
                    <wsa:MessageID>urn:uuid:4c54fcaf-61b2-8ba4-5d10-3963c531a0e7</wsa:MessageID>
                    <wsa:RelatesTo RelationshipType="http://www.w3.org/2005/08/addressing/reply">56c81f14-3a75-4cd8-b616-838bd685b366</wsa:RelatesTo>
                    <awsse:Session TransactionStatusCode="InSeries">
                        <awsse:SessionId>' . $SessionId . '</awsse:SessionId>
                        <awsse:SequenceNumber>1</awsse:SequenceNumber>
                        <awsse:SecurityToken>' . $SessionToken . '</awsse:SecurityToken>
                    </awsse:Session>
                </soap:Header>
            
                ' . $PnrRetrieve->Response_XML . '
            
            </soap:Envelope>
            ')->header('Content-Type', 'application/xml');
        }
    }

    public function docIssuanceIssueTicket()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            'awsse__Session.@attributes.TransactionStatusCode' => ['required', Rule::in('InSeries')],
            'awsse__Session.awsse__SessionId' => 'required',
            'awsse__Session.awsse__SequenceNumber' => 'required',
            'awsse__Session.awsse__SecurityToken' => 'required',
            'add__MessageID' => 'required',
            'add__Action' => 'required',
            'add__To' => 'required',
            'DocIssuance_IssueTicket.optionGroup' => 'required',
            'DocIssuance_IssueTicket.otherCompoundOptions' => 'required',
        ], [
            'awsse__Session.@attributes.TransactionStatusCode.required' => 'TransactionStatusCode',
            'awsse__Session.@attributes.TransactionStatusCode.in' => 'TransactionStatusCode',
            'awsse__Session.awsse__SessionId.required' => 'Session',
            'awsse__Session.awsse__SequenceNumber.required' => 'Session',
            'awsse__Session.awsse__SecurityToken.required' => 'Session',
            'add__MessageID.required' => 'MessageID',
            'add__Action.required' => 'Action',
            'add__To.required' => 'To',
            'DocIssuance_IssueTicket.optionGroup.required' => 'optionGroup',
            'DocIssuance_IssueTicket.otherCompoundOptions.required' => 'otherCompoundOptions',
        ]);

        ///> validation fails
        if ($validated->fails()) {
            $message = $validated->messages()->first();
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> check session
        $selectedFlight = AmadeusNewSelectedFlight::where('SessionId', $awsse__Session['awsse__SessionId'])->first();
        if (is_null($selectedFlight)) {
            $message = 'SESSION NOT FOUND';
            return response(view('AmadeusNew.5.headerError', compact('message')))->header('Content-Type', 'application/xml');
        }

        ///> session info
        $SessionId = $awsse__Session['awsse__SessionId'];
        $SessionSequenceNumber = $awsse__Session['awsse__SequenceNumber'];
        $SessionToken = $awsse__Session['awsse__SecurityToken'];
        
        ///> check pnr
        if (is_null($selectedFlight->PNRId)) {
            return response(view('AmadeusNew.13.errorData', compact('SessionId', 'SessionToken', 'SessionSequenceNumber')))->header('Content-Type', 'application/xml');
        }
        
        ///> issue the ticket
        $issueTicket = AmadeusNewPnrRetrieve::find($selectedFlight->PNRId);

        ///> check duplication
        if (!is_null($issueTicket->issue_at)) {
            return response(view('AmadeusNew.13.errorDuplicate', compact('SessionId', 'SessionToken', 'SessionSequenceNumber')))->header('Content-Type', 'application/xml');
        }

        $issueTicket->issue_at = Carbon::now();
        $issueTicket->save();

        ///> response
        return response(view('AmadeusNew.13.success', compact('SessionId', 'SessionToken', 'SessionSequenceNumber')))->header('Content-Type', 'application/xml');
    }
}
