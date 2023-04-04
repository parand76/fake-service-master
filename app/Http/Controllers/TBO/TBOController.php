<?php

namespace App\Http\Controllers\TBO;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\SampleTboResult;
use App\Models\TboAvailable;
use App\Models\TboBook;
use App\Models\TboHotelDetail;
use App\Models\TboSearch;
use App\Models\TboSelectedRoom;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TBOController extends Controller
{
    public function searchHotel()
    {
        lugInfo('hi search');
        // $s = microtime(true);

        extract(request()->all());
        $data = request()->all();
        // lugError('request for search', [request()->all()]);
        if ($errors = $this->searchValidate($data)) {
            return view('TBO.searchHotel.timeError', $errors);
        }
        repel($data['hot__HotelSearchRequest']['hot__RoomGuests']['hot__RoomGuest']);
        $countRooms = count($data['hot__HotelSearchRequest']['hot__RoomGuests']['hot__RoomGuest']);
        // lugWarning('room count in search', [request()->all(), $countRooms]);
        $roomgest = $data['hot__HotelSearchRequest']['hot__RoomGuests']['hot__RoomGuest'];

        if (isset($data['hot__HotelSearchRequest']['hot__RoomGuests']['hot__RoomGuest']['@attributes'])) {

            $adultCount = $data['hot__HotelSearchRequest']['hot__RoomGuests']['hot__RoomGuest']['@attributes']['AdultCount'];
            $childCount = $data['hot__HotelSearchRequest']['hot__RoomGuests']['hot__RoomGuest']['@attributes']['ChildCount'];
        } else {
            $adultCount = [];
            $childCount = [];
            foreach ($data['hot__HotelSearchRequest']['hot__RoomGuests']['hot__RoomGuest'] as $key => $members) {
                $adultCount[] = $members['@attributes']['AdultCount'];
            }

            $adultCount = array_sum($adultCount);
            $childCount = array_sum($childCount);
        }
        // $t = microtime(true) - $s;
        // dd($s,$t,microtime(true));
        // $s = microtime(true);

        if (isset($hot__Credentials['@attributes']['UserName']) && $hot__Credentials['@attributes']['Password']) {
            $resultRes = SampleTboResult::where('result_type', 'TBOSearch')->where('condition->cityId', $data['hot__HotelSearchRequest']['hot__CityId'])->first();
            if (empty($resultRes)) {
                $resultRes = SampleTboResult::where('result_type', 'TBOSearch')->inRandomOrder()->first();
            }
            $sessionId = uniqid("fakeTbo") . $resultRes->id;
            $response = preg_replace('/<SessionId>(.*)<\/SessionId>/', "<SessionId>$sessionId</SessionId>", $resultRes->response);
            // lugWarning('result_id and nationality that we get from search', [$resultRes, $data['hot__HotelSearchRequest']['hot__GuestNationality']]);
            $hotel = ['hotelCode' => [], 'HotelName' => []];
            $namespace = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $response);
            $array = json_decode(json_encode(simplexml_load_string($namespace)), TRUE);
            $hotel[] = [];
            foreach ($array['s__Body']['HotelSearchResponse']['HotelResultList']['HotelResult'] as $key => $r) {
                $hotel[$key+1]['hotelCode'] = $r['HotelInfo']['HotelCode'];
                $hotel[$key+1]['HotelName'] = $r['HotelInfo']['HotelName'];
            }
            $search = new TboSearch();
            $search->sample_tbo_result_id = $resultRes->id;
            $search->room_count = $countRooms;
            $search->adult_count = $adultCount;
            $search->child_count = $childCount;
            $search->infant_count = null;
            $search->checkin_date = $data['hot__HotelSearchRequest']['hot__CheckInDate'];
            $search->checkOut_date = $data['hot__HotelSearchRequest']['hot__CheckOutDate'];
            $search->sessionId = $sessionId;
            $search->hotel = $hotel;
            $search->cityId = $data['hot__HotelSearchRequest']['hot__CityId'];
            $search->nationality = $data['hot__HotelSearchRequest']['hot__GuestNationality'];
            $search->expired_at = Carbon::now()->addMinute(30);
            $search->save();
            //     $t = microtime(true) - $s;
            // dd($s,$t,microtime(true));
            lugInfo('sessionId', [$response]);


            return response($response, 200, ['Content-Type' => 'application/soap+xml; charset=utf-8']);
        }

        return response('Unkown', 499);
    }

    private function searchValidate($data)
    {

        if (
            empty($data['hot__Credentials']['@attributes']['UserName']) ||
            empty($data['hot__Credentials']['@attributes']['Password']) ||
            $data['hot__Credentials']['@attributes']['Password'] != 'Fly@51302866' ||
            $data['hot__Credentials']['@attributes']['UserName'] != "saif"
        ) {
            $code = 02;
            $error = 'LoginErr: Login Failed for Member.';
            return  compact('error', 'code');
        }

        if (empty($data['hot__HotelSearchRequest']['hot__CheckInDate']) || empty($data['hot__HotelSearchRequest']['hot__CheckOutDate'])) {
            return view('TBO.emptyType');
        }

        if (
            (Carbon::create($data['hot__HotelSearchRequest']['hot__CheckInDate'])) < Carbon::now() || (Carbon::create($data['hot__HotelSearchRequest']['hot__CheckOutDate'])) < Carbon::now()
        ) {
            $error = "ValidationErr: Invalid checkIn date 13/04/2021 00:00:00. CheckIn date must be greater than or equal to destination's today date";
            $code = 3;
            return  compact('error', 'code');
        }

        if (Carbon::create($data['hot__HotelSearchRequest']['hot__CheckOutDate']) < Carbon::create($data['hot__HotelSearchRequest']['hot__CheckInDate'])) {
            lugError('error in checkout is smalller than checkin ', [$data['hot__HotelSearchRequest']['hot__CheckOutDate'], $data['hot__HotelSearchRequest']['hot__CheckInDate']]);

            $error = "ValidationErr: Invalid date entered. CheckIn date" . $data['hot__HotelSearchRequest']['hot__CheckInDate'] . " should be less than CheckOut date" . $data['hot__HotelSearchRequest']['hot__CheckOutDate'];
            $code = 3;
            return  compact('error', 'code');
        }

        if (empty($data['hot__HotelSearchRequest']['hot__CityId'])) {

            $error = "ValidationErr: Please provide valid city ID or HotelCodeList or Geo Codes in Search Request";
            $code = 3;
            return  compact('error', 'code');
        }
        if (isset($data['hot__HotelSearchRequest']['hot__CityId'])) {

            $date = DB::connection('mysql')->table('city_supplier')->where('supplier_key', $data['hot__HotelSearchRequest']['hot__CityId'])->where('data_reference', 1202)->first();
            if (is_null($date)) {
                return view('TBO.notFoundError');
            }
        }

        if (empty($data['hot__HotelSearchRequest']['hot__GuestNationality'])) {
            $error = "ValidationErr: Nationality of the guest can not be null or empty";
            $code = 3;
            return  compact('error', 'code');
        }

        if (empty($data['hot__HotelSearchRequest']['hot__NoOfRooms'])) {
            return view('TBO.emptyType');
        }

        if ($data['hot__HotelSearchRequest']['hot__NoOfRooms'] > 6) {
            $error = "ValidationErr: You can request for at most 6 rooms.";
            $code = 3;
            return  compact('error', 'code');
        }

        $countRooms = count($data['hot__HotelSearchRequest']['hot__RoomGuests']['hot__RoomGuest']);
        if ($data['hot__HotelSearchRequest']['hot__NoOfRooms'] > $countRooms) {
            $error = "ValidationErr: RoomGuest array length should match with NoOfRooms.";
            $code = 3;
            return  compact('error', 'code');
        }
        if (isset($data['hot__HotelSearchRequest']['hot__RoomGuests']['hot__RoomGuest']['@attributes'])) {

            $adultCount = $data['hot__HotelSearchRequest']['hot__RoomGuests']['hot__RoomGuest']['@attributes']['AdultCount'];
            $childCount = $data['hot__HotelSearchRequest']['hot__RoomGuests']['hot__RoomGuest']['@attributes']['ChildCount'];

            if ($adultCount > 6) {
                $error = "ValidationErr: There can be at most 6 adults in a room.";
                $code = 3;
                return  compact('error', 'code');
            }
            if ($adultCount < 0) {
                $error = "ValidationErr: There should be at least one adult in each room.";
                $code = 3;
                return  compact('error', 'code');
            }
            if ($childCount) {
                if ((int) $childCount > 0 && empty($data['hot__HotelSearchRequest']['hot__RoomGuests']['hot__RoomGuest']['hot__ChildAge']) || $childCount != count(array($data['hot__HotelSearchRequest']['hot__RoomGuests']['hot__RoomGuest']['hot__ChildAge']['hot__int']))) {

                    $error = "ValidationErr: Every child age should be entered.";
                    $code = 3;
                    return  compact('error', 'code');
                }

                if ($childCount > 4) {

                    $error = "ValidationErr: There can be at most 4 children in a room.";
                    $code = 3;
                    return  compact('error', 'code');
                }
            }
        } else {
            $adultCount = [];
            $childCount = [];
            foreach ($data['hot__HotelSearchRequest']['hot__RoomGuests']['hot__RoomGuest'] as $key => $members) {
                $adultCount[] = $members['@attributes']['AdultCount'];

                if ($members['@attributes']['AdultCount'] > 6) {
                    $error = "ValidationErr: There can be at most 6 adults in a room.";
                    $code = 3;
                    return  compact('error', 'code');
                }

                if ($members['@attributes']['AdultCount'] < 0) {
                    $error = "ValidationErr: There should be at least one adult in each room.";
                    $code = 3;
                    return  compact('error', 'code');
                }
                if ($members['@attributes']['ChildCount']) {
                    $childCount[] = $members['@attributes']['ChildCount'];
                    if ((int)$members['@attributes']['ChildCount'] > 0 && empty($members['hot__ChildAge']) || $members['@attributes']['ChildCount'] != count(array($members['hot__ChildAge']['hot__int']))) {

                        $error = "ValidationErr: Every child age should be entered.";
                        $code = 3;
                        return  compact('error', 'code');
                    }

                    if ($members['@attributes']['ChildCount'] > 4) {

                        $error = "ValidationErr: There can be at most 4 children in a room.";
                        $code = 3;
                        return  compact('error', 'code');
                    }
                }
            }
        }

        return;
    }

    public function avilableHotels()
    {
        extract(request()->all());
        // $s = microtime(true);
        $data = request()->all();

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return response(1, 415);
        }

        $session = TboSearch::where('sessionId', $data['hot__HotelRoomAvailabilityRequest']['hot__SessionId'])->where('deleted_at', null)->first();
        lugInfo('sessionId', [$session, $data['hot__HotelRoomAvailabilityRequest']['hot__SessionId']]);

        $errors = null;
        if ($errors = $this->validationavilableHotels($data, $session)) {
            return view('TBO.availableRoom.timeError', $errors);
        }
        $countCombination = 1;
        if ($session['room_count'] > 1) {
            $countCombination = $session['room_count'];
        }


        $resultForAvilable = $this->getRoomsInfoForAvilableRoom();

        lugInfo('resultAvilable find in avilablerooms', [$resultForAvilable, $data['hot__HotelRoomAvailabilityRequest']['hot__SessionId'] != $session->sessionId, !empty($resultForAvilable), !in_array($data['hot__HotelRoomAvailabilityRequest']['hot__ResultIndex'], $resultForAvilable['indexes'])]);
        if (isset($resultForAvilable['error'])) {
            $error = $resultForAvilable['error'];
            $code = $resultForAvilable['code'];
            return view('TBO.availableRoom.timeError', compact('error', 'code'));
        }

        if (
            $data['hot__HotelRoomAvailabilityRequest']['hot__SessionId'] != $session->sessionId ||
            empty($resultForAvilable) && !in_array($data['hot__HotelRoomAvailabilityRequest']['hot__ResultIndex'], $resultForAvilable['indexes'])
        ) {
            lugError('sessionId Failed', [$session, $data['hot__HotelRoomAvailabilityRequest']['hot__SessionId']]);

            $error = "ProcessingErr: Session Expire";
            $code = 3;
            return view('TBO.availableRoom.timeError', compact('error', 'code'));
        }

        if (
            !empty($resultForAvilable) &&
            isset($data['hot__HotelRoomAvailabilityRequest']['hot__ResultIndex']) &&
            isset($data['hot__HotelRoomAvailabilityRequest']['hot__SessionId']) &&
            $data['hot__HotelRoomAvailabilityRequest']['hot__SessionId'] == $session->sessionId
        ) {
            // lugInfo('get in if');
            $canselingSharge = [];
            $checkIn = Carbon::parse($session->checkin_date);
            $checkOut = Carbon::parse($session->checkOut_date);
            $dates = $this->makeDates($checkIn, $checkOut);
            $haveCanseling = $this->haveCanselingSharge($checkIn);
            $cnaselingDeadline = $checkIn->subDays(7)->toIso8601String();

            if (isset($availableHotel['Supplements']['Supplement'])) {
                $supplement = $this->getSupplementForAvilableRoom($availableHotel['Supplements']['Supplement']);
            }

            $searchResponse = bladeRenderTbo($session->sampleTbo->response);
            $code = 0;
            $name = '';

            foreach ($searchResponse['HotelSearchResponse']['HotelResultList']['HotelResult'] as $key => $search) {
                if ($search['ResultIndex'] == $hot__HotelRoomAvailabilityRequest['hot__ResultIndex']) {
                    $code = $search['HotelInfo']['HotelCode'];
                    $name = $search['HotelInfo']['HotelName'];
                    break;
                }
            }
            lugInfo('check combination', [$countCombination]);

            if ($countCombination == 1) {
                foreach ($resultForAvilable['roomsInfo'] as $key => $room) {
                    if (Carbon::now()->diffInDays($session->checkin_date) > 7) {
                        $canselingSharge = $this->getCancelingShargeForAvilableRoom($room['CancelPolicies']['CancelPolicy']);
                    }
                    $insert[] = [
                        'tbo_search_id' => $session->id,
                        'hotel_info' => json_encode([
                            'ResultIndex' => $hot__HotelRoomAvailabilityRequest['hot__ResultIndex'],
                            'RoomTypeCode' => $room['RoomTypeCode'], 'RatePlanCode' => $room['RatePlanCode'],
                            'CancellationCharge' => $canselingSharge ?? null, 'HotelCode' => $code, 'HotelName' => $name,
                            'RoomIndex' => $room['RoomIndex'],
                            'RoomFare' => $room['TotalFare'] ?? null, 'supplement' => $supplement ?? null
                        ]),
                    ];
                }
            } else {
                // lugWarning('combination for moltyroom');
                $avilableIndexes = [];
                for ($a = 0; $a < count($resultForAvilable['indexes']); $a += $countCombination) {
                    for ($b = 0; $b < $countCombination; $b++) {
                        if (empty($resultForAvilable['indexes'][$a + $b])) {
                            break (2);
                        }
                    }
                    $roomsIndex = [];
                    for ($b = 0; $b < $countCombination; $b++) {
                        $roomsIndex[$b] = $resultForAvilable['indexes'][$a + $b];
                    }
                    $avilableIndexes[$a] = $roomsIndex;
                }

                foreach ($resultForAvilable['roomsInfo'] as $key => $room) {
                    if (Carbon::now()->diffInDays($session->checkin_date) > 7) {
                        $canselingSharge = $this->getCancelingShargeForAvilableRoom($room['CancelPolicies']['CancelPolicy']);
                    }
                    $insert[] = [
                        'tbo_search_id' => $session->id,
                        'hotel_info' => json_encode([
                            'ResultIndex' => $hot__HotelRoomAvailabilityRequest['hot__ResultIndex'],
                            'RoomTypeCode' => $room['RoomTypeCode'], 'RatePlanCode' => $room['RatePlanCode'],
                            'CancellationCharge' => $canselingSharge ?? null, 'HotelCode' => $code, 'HotelName' => $name,
                            'RoomIndex' => $room['RoomIndex'],
                            'RoomFare' => $room['TotalFare'] ?? null, 'supplement' => $supplement ?? null,
                            'combinations' => $avilableIndexes
                        ]),
                    ];
                }
                // lugWarning('insert for moltyroom', [$insert]);
            }
            // lugWarning('inserts for avilable', [$insert]);
            // lugInfo('get result avilable',[$resultForAvilable]);
            TboAvailable::insert($insert);
            // luginfo('count of rooms in avilable hotels room', [$session]);
            // lugInfo('end of avilable', ['roomsInfo' => $resultForAvilable['roomsInfo'], 'ResultIndex' => $hot__HotelRoomAvailabilityRequest['hot__ResultIndex'], 'availableHotel' => $availableHotel ?? []]);
            // lugWarning('time for avilable', [$s, $t]);
            return response(view('TBO.availableRoom.success', ['roomsInfo' => $resultForAvilable['roomsInfo'], 'indexes' => $resultForAvilable['indexes'], 'dates' => $dates ?? [], 'haveCanseling' => $haveCanseling, 'cnaselingDeadline' => $cnaselingDeadline, 'countCombination' => $countCombination ?? null]), 200, ['Content-Type' => 'application/soap+xml; charset=utf-8']);
        }

        return response('Unkown', 499);
    }

    private function getRoomsInfoForAvilableRoom()
    {

        // lugInfo('here is getRoomsInfoForAvilableRoom', []);

        $res = SampleTboResult::where('result_type', 'TBOAvailableRooms')->pluck('response')->toArray();

        // lugInfo('getRoomsInfoForAvilableRoom', [$res]);

        // $s = microtime(true);
        foreach ($res as $k => $r) {
            $namespace = preg_replace('/(\<\w+):(\w+)|(\<\/\w+):(\w+)/', '$1$3__$2$4', $r);
            $array = json_decode(json_encode(simplexml_load_string($namespace)), TRUE);
            if (empty($array['s__Body']['HotelRoomAvailabilityResponse']['HotelRooms']['HotelRoom'])) {
                continue;
            }
            $roomsInfo = [];
            $rooms = $array['s__Body']['HotelRoomAvailabilityResponse']['HotelRooms']['HotelRoom'];

            $resultGetAvilableHotel = ['indexes' => [], 'roomsInfo' => [], 'rooms' => $rooms];

            foreach ($rooms as $roomKey => $room) {
                if ($roomKey == 10) {
                    break;
                }
                $resultGetAvilableHotel['indexes'][$roomKey] = $room['RoomIndex'];
                $resultGetAvilableHotel['roomsInfo'][$roomKey] = array_intersect_key($room, array_flip(['RoomIndex', 'RoomTypeName', 'Inclusion', 'RoomTypeCode', 'RatePlanCode', 'CancelPolicies']));
                $resultGetAvilableHotel['roomsInfo'][$roomKey] = array_merge(
                    $resultGetAvilableHotel['roomsInfo'][$roomKey],
                    array_intersect_key($room['RoomRate']['@attributes'], array_flip(['RoomTax', 'TotalFare', 'Currency', 'IsPackageRate', 'IsInstantConfirmed', 'B2CRates', 'RoomFare']))
                );
            }
        }
        // $t = microtime(true) - $s;
        // dd($s,$t,microtime(true));
        return $resultGetAvilableHotel;
    }

    private function validationavilableHotels($data, $session)
    {
        if (
            empty($data['hot__Credentials']['@attributes']['UserName']) ||
            empty($data['hot__Credentials']['@attributes']['Password']) ||
            $data['hot__Credentials']['@attributes']['Password'] != 'Fly@51302866' ||
            $data['hot__Credentials']['@attributes']['UserName'] != "saif"
        ) {
            $code = 02;
            $error = 'LoginErr: Login Failed for Member.';
            return  compact('error', 'code');
        }

        if (empty($data['hot__HotelRoomAvailabilityRequest'])) {
            $error = "ProcessingErr: Session Expired";
            $code = 05;
            return  compact('error', 'code');
        }

        if (empty($data['hot__HotelRoomAvailabilityRequest']['hot__SessionId'])) {
            $error = "ProcessingErr: No Hotel Found";
            $code = 03;
            return view('TBO.availableRoom.timeError', compact('error', 'code'));
        }

        if (!$session) {
            $error = "ProcessingErr: Session Expired";
            $code = 05;
            return  compact('error', 'code');
        }
        lugInfo('session info', [$session->expired_at, Carbon::now()]);
        if (isset($session) &&  Carbon::now()->greaterThan($session->expired_at)) {

            $error = "ProcessingErr: Session Expired";
            $code = 05;
            return  compact('error', 'code');
        }

        if (empty($data['hot__HotelRoomAvailabilityRequest']['hot__ResultIndex'])) {
            $error = "ValidationErr: Index value should not be Zero or less.";
            $code = 3;
            return  compact('error', 'code');
        }

        if (empty($data['hot__HotelRoomAvailabilityRequest']['hot__HotelCode'])) {
            $error = "ValidationErr: HotelCode cannot be blank or null";
            $code = 3;
            return view('TBO.availableRoom.timeError', compact('error', 'code'));
        }
        return;
    }

    private function haveCanselingSharge($date)
    {
        // $s = microtime(true);

        $diffdaysTohaveCanseling = $date->diffInDays(Carbon::now());
        $haveCanseling = false;
        if ($diffdaysTohaveCanseling > 7) {
            $haveCanseling = true;
        }
        // $t = microtime(true) - $s;
        // dd($s,$t,microtime(true));

        return $haveCanseling;
    }

    private function getSupplementForAvilableRoom($sup)
    {
        if (isset($sup['@attributes'])) {
            $supplement = $sup['@attributes']['Price'];
        } else {
            $supplement = [];
            foreach ($sup as $suppItem) {
                $supplement[] = $suppItem['@attributes']['Price'];
            }
        }
        return $supplement;
    }

    private function getCancelingShargeForAvilableRoom($policies)
    {
        $s = microtime(true);

        if (isset($policies[0])) {
            foreach ($policies as $key => $policy) {
                $canselingSharge[] = $policy['@attributes']['CancellationCharge'];
            }
        } else {
            $canselingSharge[] = $policies['@attributes']['CancellationCharge'];
        }
        $t = microtime(true) - $s;
        // dd($s,$t,microtime(true));
        return $canselingSharge;
    }


    private function makeDates($start, $end)
    {
        $length = $start->diffInDays($end);
        $dates = [];
        for ($i = 0; $i < $length; $i++) {
            $date = $start->addDays($i);
            $dates[$i] = $date->toIso8601String();
            $dates[$i + 1] = $end->toIso8601String();
        }
        return $dates;
    }


    public function hotelDetails()
    {
        lugInfo('hi hotel detail');

        extract(request()->all());
        $data = request()->all();
        $errors = null;
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return response(1, 415);
        }
        lugInfo('hotel detail request',[$data['hot__HotelDetailsRequest']]);
        if (!empty($data['hot__HotelDetailsRequest']['hot__HotelCode'])) {
            $search = TboSearch::where('sessionId', $data['hot__HotelDetailsRequest']['hot__SessionId'])->where('deleted_at', null)->first();
            lugInfo('search find',[$search]);
            $hotels = $search->hotel;
            $hotel=$hotels[$data['hot__HotelDetailsRequest']['hot__ResultIndex']];
            lugInfo('hotel find for debug detail',[$hotel,$data['hot__HotelDetailsRequest']['hot__ResultIndex']]);
            $rate = rand(2, 5);
            $country=null;
            // $city = DB::table('city_supplier')->where('data_reference', 1202)->where('supplier_key', $search->cityId)->first();
        }

        if ($errors = $this->validationHotelDetail($data)) {
            return view('TBO.hotelDetail.timeError', $errors);
        }

        if (
            isset($data['hot__Credentials']['@attributes']['UserName']) && isset($data['hot__Credentials']['@attributes']['Password']) &&
            isset($hot__HotelDetailsRequest['hot__HotelCode'])
        ) {
            lugWarning('last part of hotel detail', [$hotel, $rate, $country]);

            return response(view('TBO.hotelDetail.successHotelDetail', ['hotel' => $hotel, 'rate' => $rate, 'country' => $country]), 200, ['Content-Type' => 'application/soap+xml; charset=utf-8']);
            // return response(view('TBO.hotelDetail.successHotelDetail', ['detail' => $detail]), 200, ['Content-Type' => 'application/soap+xml; charset=utf-8']);
        }

        return response('Unkown', 499);
    }

    private function validationHotelDetail($data)
    {
        if (
            empty($data['hot__Credentials']['@attributes']['UserName']) ||
            empty($data['hot__Credentials']['@attributes']['Password']) ||
            $data['hot__Credentials']['@attributes']['Password'] != 'Fly@51302866' ||
            $data['hot__Credentials']['@attributes']['UserName'] != "saif"
        ) {
            $code = 02;
            $error = 'LoginErr: Login Failed for Member.';
            return  compact('error', 'code');
        }

        if (empty($data['hot__HotelDetailsRequest']['hot__HotelCode'])) {
            $error = "ValidationErr: Please provide either HotelCode or sessionId and result index";
            $code = 03;
            return  compact('error', 'code');
        }

        return;
    }

    public function availibilityAndPricing()
    {
        // $s = microtime(true);
        extract(request()->all());
        $data = request()->all();

        $errors = null;
        $search = null;
        $hotel = null;
        $hotelIdes = [];
        if (!empty($data['hot__AvailabilityAndPricingRequest']['hot__SessionId'])) {
            $roomIndex = null;
            $roomIndexes = null;
            $search = TboSearch::where('sessionId', $data['hot__AvailabilityAndPricingRequest']['hot__SessionId'])->where('deleted_at', null)->first();
            // lugInfo('search is found in pricing avilable', [$search]);
            if (!empty($search)) {
                $hotels = TboAvailable::where('tbo_search_id', $search->id)->get();
                // lugInfo('hotels are found', [$hotels]);
                if (!empty($hotels)) {
                    foreach ($hotels as $hotelKey => $hotel) {
                        if (is_array($data['hot__AvailabilityAndPricingRequest']['hot__OptionsForBooking']['hot__RoomCombination']['hot__RoomIndex'])) {
                            if (isset($hotel->hotel_info['combinations'])) {
                                foreach ($hotel->hotel_info['combinations'] as $combinkey => $combin) {
                                    if ($data['hot__AvailabilityAndPricingRequest']['hot__OptionsForBooking']['hot__RoomCombination']['hot__RoomIndex'] == $combin) {
                                        $roomIndexes = $data['hot__AvailabilityAndPricingRequest']['hot__OptionsForBooking']['hot__RoomCombination']['hot__RoomIndex'];
                                        break;
                                    }
                                }
                            } else {
                                // TODO
                                $error = "ValidationErr: Room index count in OptionsForBooking is not valid";
                                $code = 03;
                                return   response(view('TBO.availibilityAndPricing.timeError', compact('error', 'code')));
                            }
                            foreach ($roomIndexes as $keyhotelId => $index) {
                                if ($hotel->hotel_info['RoomIndex'] == $index) {
                                    $hotelIdes[$keyhotelId] = $hotel->id;
                                }
                            }
                        } else {
                            if ($data['hot__AvailabilityAndPricingRequest']['hot__OptionsForBooking']['hot__RoomCombination']['hot__RoomIndex'] == $hotel->hotel_info['RoomIndex']) {
                                $roomIndex = $data['hot__AvailabilityAndPricingRequest']['hot__OptionsForBooking']['hot__RoomCombination']['hot__RoomIndex'];
                                $hotelId = $hotelKey;
                                break;
                            }
                        }
                    }
                }
            }
            // $t = microtime(true) - $s;
            // dd([$s, $t,microtime(true)]);

            $rooms = null;
            if (!empty($roomIndex)) {
                // lugInfo('roomIndex in pricing', [$roomIndex]);
                $hotel = TboAvailable::where('tbo_search_id', $search->id)->where('hotel_info->RoomIndex', $roomIndex)->first();
                // lugDebug('finally hotel find in pricing', [$hotel]);
                // lugInfo('this hotel have canseling sharge in pricing', [$hotel->hotel_info['CancellationCharge']]);
            }
            if (!empty($hotelIdes)) {
                foreach ($hotelIdes as $id) {
                    $rooms[] = TboAvailable::where('tbo_search_id', $search->id)->where('id', $id)->first();
                }
            }
            // lugInfo('search=session is find ', [$search]);
        }
        // $s = microtime(true);

        if (empty($rooms)) {
            if ($errors = $this->validationAvailibilityAndPricing($data, $search, $hotel)) {
                return view('TBO.availibilityAndPricing.timeError', $errors);
            }
        }
        // $t = microtime(true) - $s;
        // dd([$s, $t,microtime(true)]);
        if (isset($data['hot__AvailabilityAndPricingRequest']['hot__ResultIndex']) && $data['hot__AvailabilityAndPricingRequest']['hot__ResultIndex'] == []) {
            lugError('hot__ResultIndex is empty in pricing', [$data['hot__AvailabilityAndPricingRequest']['hot__ResultIndex']]);
            return response(view('TBO.500Error'), 500);
        }

        if (!in_array($data['hot__AvailabilityAndPricingRequest']['hot__OptionsForBooking']['hot__FixedFormat'], ['false', 'true'])) {
            lugError('hot__FixedFormat is not true or false in pricing', [$data['hot__AvailabilityAndPricingRequest']['hot__OptionsForBooking']['hot__FixedFormat']]);
            return response(view('TBO.500Error'), 500);
        }

        if (
            isset($hot__Credentials['@attributes']['UserName']) && isset($hot__Credentials['@attributes']['Password']) &&
            isset($data['hot__AvailabilityAndPricingRequest']['hot__SessionId']) && isset($data['hot__AvailabilityAndPricingRequest']['hot__ResultIndex']) && ($data['hot__AvailabilityAndPricingRequest']['hot__OptionsForBooking']['hot__FixedFormat'] == "true" || $hot__AvailabilityAndPricingRequest['hot__OptionsForBooking']['hot__FixedFormat'] == "false")
        ) {
            $bookable = "true";
            if (empty($rooms)) {
                // lugInfo('bookable and holdable', [$hotel->hotel_info['CancellationCharge'], $bookable]);
                if (in_array(0, $hotel->hotel_info['CancellationCharge'])) {
                    $holdable = "true";
                    // lugInfo('book in holdable pricing', [$hotel->hotel_info['CancellationCharge'], $bookable], $holdable);
                } else {
                    $holdable = "false";
                    // lugInfo('book in notholdable pricing', [$hotel->hotel_info['CancellationCharge'], $holdable]);
                }
                $selected = new TboSelectedRoom();
                $selected->selectedRooms_id = $hotel->id;
                $selected->tbo_search_id = $search->id;
                $selected->hotel_info = $hotel->hotel_info;
                $selected->save();
                lugWarning('pricing section sevaed in tboselected rooms is', [$hotel->id, $search->id]);
            } else {
                // lugInfo('bookable and holdable for moltyRoom pricing', [$bookable]);
                $canselable = [];
                $roomsInfoes = [];
                foreach ($rooms as $room) {
                    if (in_array(0, $room->hotel_info['CancellationCharge'])) {
                        $canselable[] = "true";
                    } else {
                        $canselable[] = "false";
                    }
                    $roomsInfoes[] = $room->hotel_info;
                }
                if (in_array("false", $canselable)) {
                    $holdable = "true";
                    lugInfo('book in holdable moltyroom pricing', [$rooms, $canselable, $bookable, $holdable]);
                } else {
                    $holdable = "false";
                    lugInfo('book in notholdable moltyroom pricing', [$rooms, $canselable, $holdable]);
                }

                $selected = new TboSelectedRoom();
                $selected->selectedRooms_id = implode(',', $hotelIdes);
                $selected->tbo_search_id = $search->id;
                $selected->hotel_info = $roomsInfoes;
                $selected->save();
                lugWarning('pricing section sevaed in tboselected rooms is moltyroom', [$hotelIdes, $roomsInfoes, $search->id]);
            }
            return response(view('TBO.availibilityAndPricing.successAvilabilityAndPricing', compact('bookable', 'holdable')), 200, ['Content-Type' => 'application/soap+xml; charset=utf-8']);
        }
        return response('Unkown', 499);
    }

    private function validationAvailibilityAndPricing($data, $search, $hotel)
    {
        if (
            empty($data['hot__Credentials']['@attributes']['UserName']) ||
            empty($data['hot__Credentials']['@attributes']['Password']) ||
            $data['hot__Credentials']['@attributes']['Password'] != 'Fly@51302866' ||
            $data['hot__Credentials']['@attributes']['UserName'] != "saif"
        ) {
            $code = 02;
            $error = 'LoginErr: Login Failed for Member.';
            return  compact('error', 'code');
        }

        if (empty($data['hot__AvailabilityAndPricingRequest']['hot__SessionId'])) {
            $error = "ProcessingErr: Session Expired";
            $code = 05;
            return  compact('error', 'code');
        }

        if (empty($data['hot__AvailabilityAndPricingRequest']['hot__ResultIndex'])) {
            $error = "ValidationErr: Index value should not be Zero or less.";
            $code = 03;
            return  compact('error', 'code');
        }
        if (empty($search) || empty($hotel)) {
            lugError('search or hotel not find', [$search, $hotel]);
            $error = "ProcessingErr: Session Expired";
            $code = 05;
            return  compact('error', 'code');
        }

        if (isset($search) && $search->expired_at < Carbon::now()) {
            $error = "ProcessingErr: Session Expired";
            $code = 05;
            return  compact('error', 'code');
        }
        if (is_array($data['hot__AvailabilityAndPricingRequest']['hot__OptionsForBooking']['hot__RoomCombination']['hot__RoomIndex'])) {
            lugInfo('moltyroom', [$data['hot__AvailabilityAndPricingRequest']['hot__OptionsForBooking']['hot__RoomCombination']['hot__RoomIndex']]);
            if ((count($data['hot__AvailabilityAndPricingRequest']['hot__OptionsForBooking']['hot__RoomCombination']['hot__RoomIndex'])) != $search->room_count) {
                lugError('eror for roomCount for many room', [count($data['hot__AvailabilityAndPricingRequest']['hot__OptionsForBooking']['hot__RoomCombination']), $search->room_count]);
                $error = "ValidationErr: Room index count in OptionsForBooking is not valid";
                $code = 03;
                return  compact('error', 'code');
            }
        } else {
            // dd(count($data['hot__AvailabilityAndPricingRequest']['hot__OptionsForBooking']['hot__RoomCombination']),$search->room_count);
            if ((count($data['hot__AvailabilityAndPricingRequest']['hot__OptionsForBooking']['hot__RoomCombination'])) != $search->room_count) {
                lugError('eror for roomCount for one room', [count($data['hot__AvailabilityAndPricingRequest']['hot__OptionsForBooking']['hot__RoomCombination']), $search->room_count]);

                $error = "ValidationErr: Room index count in OptionsForBooking is not valid";
                $code = 03;
                return  compact('error', 'code');
            }
        }
        return;
    }

    public function bookHotel()
    {


        extract(request()->all());
        $data = request()->all();
        // lugInfo('request data of book', [$data]);

        $errors = null;
        $search = null;
        $hotel = null;
        if (!empty($data['hot__HotelBookRequest']['hot__SessionId'])) {
            $search = TboSearch::where('sessionId', $data['hot__HotelBookRequest']['hot__SessionId'])->where('deleted_at', null)->first();
            // lugInfo('search=session is find in bookhotel ', [$search]);
            if (!empty($search)) {
                $hotel = TboSelectedRoom::where('tbo_search_id', $search->id)->first();
                // lugInfo('hotel of book', [$hotel]);
            }
        }
        if ($errors = $this->validationBookHotel($data, $search, $hotel)) {
            return view('TBO.bookHotel.timeError', $errors);
        }


        if (isset($data['hot__HotelBookRequest']['hot__Guests']['hot__Guest']['@attributes'])) {
            // $s = microtime(true);

            if ($errors = $this->validationBookHotelForOneGest($data)) {
                return view('TBO.bookHotel.timeError', $errors);
            }
            // $t = microtime(true) - $s;
            // dd([$s, $t,microtime(true)],99);
            if (empty($data['hot__HotelBookRequest']['hot__Guests']['hot__Guest']['hot__Age'])) {
                lugError('hot__Age is empty for one gest', [$data['hot__HotelBookRequest']['hot__Guests']['hot__Guest']['hot__Age']]);
                return response(view('TBO.500Error'), 500);
            }
        } else {
            foreach ($data['hot__HotelBookRequest']['hot__Guests']['hot__Guest'] as $key => $guest) {
                if ($errors = $this->validationBookHotelForManyGests($guest)) {
                    return view('TBO.bookHotel.timeError', $errors);
                }
                if (empty($guest['hot__Age'])) {
                    lugError('hot__Age is empty for many gest', [$guest['hot__Age']]);
                    return response(view('TBO.500Error'), 500);
                }
            }
        }

        if (empty($data['hot__HotelBookRequest']['hot__PaymentInfo']['@attributes']['VoucherBooking'])) {
            lugError('VoucherBooking is empty', [$data['hot__HotelBookRequest']['hot__PaymentInfo']['@attributes']['VoucherBooking']]);
            return response(view('TBO.500Error'), 500);
        }

        if (empty($data['hot__HotelBookRequest']['hot__ResultIndex'])) {
            return response(view('TBO.500Error'), 500);
        }

        // $s = microtime(true);

        // =====================================================================
        if ($errors = $this->checkRoom($data, $search, $hotel)) {
            return $errors;
        }
        // ==============================================================
        // $t = microtime(true) - $s;
        // dd([$s, $t,microtime(true)]);
        if (isset($data['hot__HotelBookRequest']['hot__HotelRooms'])) {

            if ($hot__HotelBookRequest['hot__ResultIndex']) {
                $code = 01;
                $confirmationNumber = uniqid();
                $message = "Successful: HotelBook Successful";
                if (isset($hotel->hotel_info[0])) {
                    $canselingSharge = [];
                    foreach ($hotel->hotel_info as $hotelInfo) {
                        $canselingSharge[] = $hotelInfo['CancellationCharge'];
                    }
                    $holdable = [];
                    foreach ($canselingSharge as $canseling) {
                        if (in_array(0, $canseling)) {
                            $holdable[] = "true";
                        } else {
                            $holdable[] = "false";
                        }
                    }
                    if (!in_array("false", $holdable)) {
                        $book = new TboBook();
                        $book->tbo_selected_available_id = $hotel->id;
                        $book->code = $confirmationNumber;
                        $book->expierd_at = Carbon::now()->addMinute(7);
                        $book->save();
                        lugWarning('hotel book on hold is do for moltyroom', [$hot__HotelBookRequest['hot__PaymentInfo']['@attributes']['VoucherBooking'], $hotel->hotel_info]);
                        return response(view("TBO.bookHotel.success", compact('message', 'confirmationNumber', 'code')), 200, ['Content-Type' => 'application/soap+xml; charset=utf-8']);
                    } else {
                        lugWarning('hotel book issue is do moltyroom notHoldable', [$hot__HotelBookRequest['hot__PaymentInfo']['@attributes']['VoucherBooking'], $hotel->hotel_info]);
                        return response(view("TBO.bookHotel.success", compact('message', 'confirmationNumber', 'code')), 200, ['Content-Type' => 'application/soap+xml; charset=utf-8']);
                    }
                }
                // lugWarning('check book hold', [$hot__HotelBookRequest['hot__PaymentInfo']['@attributes']['VoucherBooking'], $hotel->hotel_info]);
                if (
                    $hot__HotelBookRequest['hot__PaymentInfo']['@attributes']['VoucherBooking'] == "false" &&
                    in_array(0, $hotel->hotel_info['CancellationCharge'])
                ) {
                    $book = new TboBook();
                    $book->tbo_selected_available_id = $hotel->id;
                    $book->code = $confirmationNumber;
                    $book->expierd_at = Carbon::now()->addMinute(7);
                    $book->save();
                    lugWarning('hotel book on hold is do', [$hot__HotelBookRequest['hot__PaymentInfo']['@attributes']['VoucherBooking'], $hotel->hotel_info['CancellationCharge']]);
                    return response(view("TBO.bookHotel.success", compact('message', 'confirmationNumber', 'code')), 200, ['Content-Type' => 'application/soap+xml; charset=utf-8']);
                } else {
                    lugWarning('hotel book issue is do notHoldable', [$hot__HotelBookRequest['hot__PaymentInfo']['@attributes']['VoucherBooking'], $hotel->hotel_info['CancellationCharge']]);
                    return response(view("TBO.bookHotel.success", compact('message', 'confirmationNumber', 'code')), 200, ['Content-Type' => 'application/soap+xml; charset=utf-8']);
                }
            }
        }
        return response('Unkown', 499);
    }

    private function checkRoom($data, $search, $hotel)
    {
        if (isset($data['hot__HotelBookRequest']['hot__HotelRooms'])) {
            if (count($data['hot__HotelBookRequest']['hot__HotelRooms']['hot__HotelRoom']) < $search->room_count) {
                lugError('hot__HotelRoom hot__HotelRoom count not equal with search', [$data['hot__HotelBookRequest']['hot__NoOfRooms'], $search->room_count]);
                $code = 05;
                $error = "ProcessingErr: The combination sent from the user end is not proper";
                return view('TBO.bookHotel.timeError', compact('error', 'code'));
            }
            if (isset($data['hot__HotelBookRequest']['hot__HotelRooms']['hot__HotelRoom'][0])) {
                if (count($data['hot__HotelBookRequest']['hot__HotelRooms']['hot__HotelRoom']) != $search->room_count) {
                    lugError('hot__HotelRoom count not equal with search', [$data['hot__HotelBookRequest']['hot__NoOfRooms'], $search->room_count]);
                    $code = 03;
                    $error = "ValidationErr: Adult count mismatch between search and book request for room index: 2";
                    return view('TBO.bookHotel.timeError', compact('error', 'code'));
                }
                if ($errors = $this->validationBookHotelForManyRoom($data['hot__HotelBookRequest']['hot__HotelRooms']['hot__HotelRoom'], $hotel)) {
                    return view('TBO.bookHotel.timeError', $errors);
                }
                foreach ($data['hot__HotelBookRequest']['hot__HotelRooms']['hot__HotelRoom'] as $key => $room) {
                    if (empty($room['hot__RoomIndex'])) {
                        lugError('hot__RoomIndex is empty for room in moltyRoom,error500', [$room['hot__RoomIndex']]);
                        return response(view('TBO.500Error'), 500);
                    }
                    if (!empty($room['hot__Supplements'])) {

                        if (empty($room['hot__Supplements']['hot__SuppInfo']['@attributes']['Price'])) {
                            lugError('price is empty for molty room,error500', [$room]);
                            return response(view('TBO.500Error'), 500);
                        }
                    }
                    if (isset($hotel->hotel_info['supplement'])) {
                        if ($errors = $this->checkSupplement($room, $hotel)) {
                            return view('TBO.bookHotel.timeError', $errors);
                        }
                    }
                }
            } else {
                $room = $data['hot__HotelBookRequest']['hot__HotelRooms']['hot__HotelRoom'];
                if (empty($room['hot__RoomIndex'])) {
                    lugError('hot__RoomIndex is empty for room,error500', [$room['hot__RoomIndex']]);
                    return response(view('TBO.500Error'), 500);
                }
                if ($errors = $this->validationBookHotelForRoom($room, $hotel)) {
                    return view('TBO.bookHotel.timeError', $errors);
                }
                if (isset($hotel->hotel_info['supplement'])) {
                    if ($errors = $this->checkSupplement($room, $hotel)) {
                        return view('TBO.bookHotel.timeError', $errors);
                    }
                    if (empty($room['hot__Supplements']['hot__SuppInfo']['@attributes']['Price'])) {
                        lugError('price is empty for room,error500', [$room['hot__Supplements']['hot__SuppInfo']['@attributes']['Price']]);

                        return response(view('TBO.500Error'), 500);
                    }
                }
                if (empty($room['hot__RoomRate']['@attributes']['RoomFare'])) {
                    lugError('room fare is empty for room,error500', [$room['hot__RoomRate']['@attributes']['RoomFare']]);

                    return response(view('TBO.500Error'), 500);
                }
            }
        }
        return;
    }

    private function validationBookHotel($data, $search, $hotel)
    {
        // lugWarning('data for validate in book', [$data, $search, $hotel]);
        if (
            empty($data['hot__Credentials']['@attributes']['UserName']) ||
            empty($data['hot__Credentials']['@attributes']['Password']) ||
            $data['hot__Credentials']['@attributes']['Password'] != 'Fly@51302866' ||
            $data['hot__Credentials']['@attributes']['UserName'] != "saif"
        ) {
            $code = 02;
            $error = 'LoginErr: Login Failed for Member.';
            return  compact('error', 'code');
        }

        if (empty($data['hot__HotelBookRequest']['hot__GuestNationality'])) {
            lugError('nationality is empty', [$data['hot__HotelBookRequest']['hot__GuestNationality']]);
            $code = 03;
            $error = "ValidationErr: Please provide a valid GuestNationality of length 2 characters";
            return  compact('error', 'code');
        }
        if (empty($search)) {
            lugError('search not found', [$search]);
            $error = "ProcessingErr: Session Expired";
            $code = 05;
            return  compact('error', 'code');
        }

        if (empty($hotel)) {
            lugError('hotel not found', [$hotel]);
            $error = "ProcessingErr: This itinerary is not available";
            $code = 05;
            return  compact('error', 'code');
        }

        // lugInfo('booking search is find ', [$hotel]);

        if ($data['hot__HotelBookRequest']['hot__GuestNationality'] != $search->nationality) {
            lugError('nationality error', [$data['hot__HotelBookRequest']['hot__GuestNationality'], $search->nationality]);
            $code = 03;
            $error = "ValidationErr: GuestNationality should be same as provided in Search Request";
            return  compact('error', 'code');
        }

        if (empty($data['hot__HotelBookRequest']['hot__SessionId'])) {
            $error = "ProcessingErr: Session Expired";
            $code = 05;
            return  compact('error', 'code');
        }
        if (isset($search) && $search->expired_at < Carbon::now()) {
            $error = "ProcessingErr: Session Expired";
            $code = 05;
            return  compact('error', 'code');
        }
        if (empty($data['hot__HotelBookRequest']['hot__NoOfRooms'])) {
            $code = 05;
            $error = "ProcessingErr: StartIndex cannot be less than zero'.&#xD;'Parameter name: startIndex";
            return  compact('error', 'code');
        }

        if ($data['hot__HotelBookRequest']['hot__NoOfRooms'] < $search->room_count) {
            lugError('hot__NoOfRooms is smaller whit search', [$data['hot__HotelBookRequest']['hot__NoOfRooms'], $search->room_count]);
            $code = 05;
            $error = "ValidationErr: Number of rooms requested should match the HotelRooms provided in book request";
            return  compact('error', 'code');
        }

        if ($data['hot__HotelBookRequest']['hot__NoOfRooms'] > $search->room_count) {
            lugError('hot__NoOfRooms is bigger than search', [$data['hot__HotelBookRequest']['hot__NoOfRooms']]);
            $code = 04 - 15;
            $error = "SystemErr: Technical Failure";
            return  compact('error', 'code');
        }
        if (!empty($hotel->hotel_info[0])) {
            lugWarning('validate book and there is hotel_info more than one (molty room)');
            if ($data['hot__HotelBookRequest']['hot__ResultIndex'] != $hotel->hotel_info[0]['ResultIndex']) {
                lugError('hot__ResultIndex in request not equl whit ResultIndex in hotelInfo in molty room', [$data['hot__HotelBookRequest']['hot__ResultIndex'], $hotel->hotel_info[0]['ResultIndex']]);
                $code = 05;
                $error = "ProcessingErr: Hotelresult not loaded from Cache, This may be because cache data has expired";
                return  compact('error', 'code');
            }
        } else {
            // lugWarning('validate book and there is hotel_info just one  (one room)');
            if ($data['hot__HotelBookRequest']['hot__ResultIndex'] != $hotel->hotel_info['ResultIndex']) {
                lugError('hot__ResultIndex in request not equl whit ResultIndex in hotelInfo in one room', [$data['hot__HotelBookRequest']['hot__ResultIndex'], $hotel->hotel_info['ResultIndex']]);
                $code = 05;
                $error = "ProcessingErr: Hotelresult not loaded from Cache, This may be because cache data has expired";
                return  compact('error', 'code');
            }
        }
        return;
    }

    private function validationBookHotelForOneGest($data)
    {
        if (empty($data['hot__HotelBookRequest']['hot__Guests']['hot__Guest']['hot__Title']) || !in_array($data['hot__HotelBookRequest']['hot__Guests']['hot__Guest']['hot__Title'], ['Mr', 'Ms', 'Mrs', 'Miss', 'mr', 'ms', 'mrs', 'miss'])) {
            lugError('title failed', [$data['hot__HotelBookRequest']['hot__Guests']['hot__Guest']['hot__Title'], !in_array($data['hot__HotelBookRequest']['hot__Guests']['hot__Guest']['hot__Title'], ['Mr', 'Ms', 'Mrs', 'Miss', 'mr', 'ms', 'mrs', 'miss'])]);
            $code = 03;
            $error = "ValidationErr: Guest No 1: Title should not be null or empty or it should be one of the supported Titles ( Mr, Ms, Mrs, Miss ).";
            return  compact('error', 'code');
        }

        if (empty($data['hot__HotelBookRequest']['hot__Guests']['hot__Guest']['hot__FirstName'])) {
            $code = 03;
            $error = "ValidationErr: Guest No 1: First name should not be null, empty string or its length less than 2.(Valid value : alphabetic name with one space allowed)";
            return  compact('error', 'code');
        }

        if (empty($data['hot__HotelBookRequest']['hot__Guests']['hot__Guest']['hot__LastName'])) {
            $code = 03;
            $error = "ValidationErr: Guest No 1: Last name should not be null, empty string or its length less than 2.(Valid value : alphabetic name with one space allowed)";
            return  compact('error', 'code');
        }
        return;
    }

    private function validationBookHotelForManyGests($gest)
    {
        if (empty($gest['hot__Title']) || !in_array($gest['hot__Title'], ['Mr', 'Ms', 'Mrs', 'Miss', 'mr', 'ms', 'mrs', 'miss'])) {
            lugError('title for many gests', [$gest['hot__Title']]);
            $code = 03;
            $error = "ValidationErr: gest No 1: Title should not be null or empty or it should be one of the supported Titles ( Mr, Ms, Mrs, Miss ).";
            return  compact('error', 'code');
        }

        if (empty($gest['hot__FirstName'])) {
            $code = 03;
            $error = "ValidationErr: gest No 1: First name should not be null, empty string or its length less than 2.(Valid value : alphabetic name with one space allowed)";
            return  compact('error', 'code');
        }

        if (empty($gest['hot__LastName'])) {

            $code = 03;
            $error = "ValidationErr: gest No 1: Last name should not be null, empty string or its length less than 2.(Valid value : alphabetic name with one space allowed)";
            return  compact('error', 'code');
        }
    }
    private function validationBookHotelForManyRoom($rooms, $hotel)
    {
        foreach ($rooms as $room) {
            foreach ($hotel->hotel_info as $info) {
                if ($room['hot__RoomIndex'] != $info['RoomIndex']) {
                    lugError('error in book for RoomIndex moltyroom', [$room['hot__RoomIndex'], $info['RoomIndex']]);
                    $code = 04 - 15;
                    $error = "SystemErr: Technical Failure";
                    return  compact('error', 'code');
                }
                if (empty($room['hot__RoomTypeCode']) || $room['hot__RoomTypeCode'] != $info['RoomTypeCode']) {
                    lugError('error in boook for roomType code', [$room['hot__RoomTypeCode'], $info['RoomTypeCode']]);
                    $code = 05;
                    $error = "ProcessingErr: The combination sent from the user end is not proper";
                    return  compact('error', 'code');
                }
                if (empty($room['hot__RatePlanCode']) || $room['hot__RatePlanCode'] != $info['RatePlanCode']) {
                    lugError('error in boook for roomRatePlanCode code', [$room['hot__RatePlanCode'], $info['RatePlanCode']]);
                    $code = 05;
                    $error = "ProcessingErr: The combination sent from the user end is not proper";
                    return  compact('error', 'code');
                }
                if ($room['hot__RoomRate']['@attributes']['RoomFare'] != $info['RoomFare']) {
                    lugError('error in boook for room RoomFare code', [$room['hot__RoomRate']['@attributes']['RoomFare'], $info['RoomFare']]);
                    $code = 05;
                    $error = "ProcessingErr: The combination sent from the user end is not proper";
                    return  compact('error', 'code');
                }
                return;
            }
        }
    }
    private function validationBookHotelForRoom($room, $hotel)
    {
        if (!empty($hotel->hotel_info[0])) {
            // lugWarning('data for validate in rooms for moltyroom', $room, $hotel);
            foreach ($hotel->hotel_info as $info) {
                if ($room['hot__RoomIndex'] != $info['RoomIndex']) {
                    lugError('error in book for RoomIndex moltyroom', [$room['hot__RoomIndex'], $info['RoomIndex']]);
                    $code = 04 - 15;
                    $error = "SystemErr: Technical Failure";
                    return  compact('error', 'code');
                }
                if (empty($room['hot__RoomTypeCode']) || $room['hot__RoomTypeCode'] != $info['RoomTypeCode']) {
                    lugError('error in boook for roomType code', [$room['hot__RoomTypeCode'], $info['RoomTypeCode']]);
                    $code = 05;
                    $error = "ProcessingErr: The combination sent from the user end is not proper";
                    return  compact('error', 'code');
                }
                if (empty($room['hot__RatePlanCode']) || $room['hot__RatePlanCode'] != $info['RatePlanCode']) {
                    lugError('error in boook for roomRatePlanCode code', [$room['hot__RatePlanCode'], $info['RatePlanCode']]);
                    $code = 05;
                    $error = "ProcessingErr: The combination sent from the user end is not proper";
                    return  compact('error', 'code');
                }
                if ($room['hot__RoomRate']['@attributes']['RoomFare'] != $info['RoomFare']) {
                    lugError('error in boook for room RoomFare code', [$room['hot__RoomRate']['@attributes']['RoomFare'], $info['RoomFare']]);
                    $code = 05;
                    $error = "ProcessingErr: The combination sent from the user end is not proper";
                    return  compact('error', 'code');
                }
                return;
            }
        } else {
            // lugWarning('data for validate in rooms', $room, $hotel);
            if ($room['hot__RoomIndex'] != $hotel->hotel_info['RoomIndex']) {
                lugError('error in book for RoomIndex', [$room['hot__RoomIndex'], $hotel->hotel_info['RoomIndex']]);
                $code = 04 - 15;
                $error = "SystemErr: Technical Failure";
                return  compact('error', 'code');
            }
            if (empty($room['hot__RoomTypeCode']) || $room['hot__RoomTypeCode'] != $hotel->hotel_info['RoomTypeCode']) {
                lugError('error in boook for roomType code', [$room['hot__RoomTypeCode'], $hotel->hotel_info['RoomTypeCode']]);
                $code = 05;
                $error = "ProcessingErr: The combination sent from the user end is not proper";
                return  compact('error', 'code');
            }

            if (empty($room['hot__RatePlanCode']) || $room['hot__RatePlanCode'] != $hotel->hotel_info['RatePlanCode']) {
                lugError('error in boook for roomRatePlanCode code', [$room['hot__RatePlanCode'], $hotel->hotel_info['RatePlanCode']]);
                $code = 05;
                $error = "ProcessingErr: The combination sent from the user end is not proper";
                return  compact('error', 'code');
            }
            if ($room['hot__RoomRate']['@attributes']['RoomFare'] != $hotel->hotel_info['RoomFare']) {
                lugError('error in boook for room RoomFare code', [$room['hot__RoomRate']['@attributes']['RoomFare'], $hotel->hotel_info['RoomFare']]);
                $code = 05;
                $error = "ProcessingErr: The combination sent from the user end is not proper";
                return  compact('error', 'code');
            }

            return;
        }
    }

    private function checkSupplement($room)
    {
        if (empty($room['hot__Supplements'])) {
            $code = 05;
            $error = "ProcessingErr: Agent should agree on AtProperty supplements and included them in request";
            return  compact('error', 'code');
        }
        if (empty($room['hot__Supplements']['hot__SuppInfo'])) {
            $code = 05;
            $error = "ProcessingErr: Agent should agree on all the AtProperty supplements and included them in request";
            return  compact('error', 'code');
        }
        return;
    }

    public function countryList()
    {
        extract(request()->all());

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return response(1, 415);
        }

        if (
            empty($hot__Credentials['@attributes']['UserName']) ||
            empty($hot__Credentials['@attributes']['Password']) ||
            $hot__Credentials['@attributes']['Password'] != 'Fly@51302866' ||
            $hot__Credentials['@attributes']['UserName'] != "saif"
        ) {
            $code = 02;
            $error = 'LoginErr: Login Failed for Member.';
            return response(view('TBO.hotelDetail.timeError', compact('error', 'code')), 200, ['Content-Type' => 'application/soap+xml; charset=utf-8']);
        }

        if (isset($hot__Credentials['@attributes']['UserName']) && isset($hot__Credentials['@attributes']['Password'])) {
            return response(view('TBO.countryList.success'), 200, ['Content-Type' => 'application/soap+xml; charset=utf-8']);
        }
    }

    public function generateInvoice()
    {

        extract(request()->all());

        if (empty($hot__GenerateInvoiceRequest['hot__ConfirmationNo'])) {
            $code = 03;
            $error = "ValidationErr: Please provide BookingId or Confirmation No, both cannot be null or blank";
            return response(view('TBO.generateInvoice.timeError', compact('error', 'code')), 200, ['Content-Type' => 'application/soap+xml; charset=utf-8']);
        }

        $book = TboBook::where('code', $hot__GenerateInvoiceRequest['hot__ConfirmationNo'])->first();
        if (!isset($book)) {
            $code = 03;
            $error = "ProcessingErr: No Itinerary exist for this request";
            return response(view('TBO.generateInvoice.timeError', compact('error', 'code')), 200, ['Content-Type' => 'application/soap+xml; charset=utf-8']);
        }

        if (isset($book) && $book->expierd_at < Carbon::now()) {
            $code = 05;
            $error = "ProcessingErr: Invoice for this booking is already generated.";
            return response(view('TBO.generateInvoice.timeError', compact('error', 'code')), 200, ['Content-Type' => 'application/soap+xml; charset=utf-8']);
        }

        if (isset($hot__GenerateInvoiceRequest['hot__ConfirmationNo']) && isset($book) && $book->expierd_at > Carbon::now()) {
            $code = $book->code;
            return response(view('TBO.generateInvoice.success', compact('code')), 200, ['Content-Type' => 'application/soap+xml; charset=utf-8']);
        }
    }
}
