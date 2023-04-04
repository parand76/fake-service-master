<?php

namespace App\Http\Controllers\ISMS;

use App\Http\Controllers\Controller;

class SmsController extends Controller
{
    public function send()
    {
        lugInfo('reciving data in isms',[request()->all()]);
        extract(request()->all());
        if (empty($username) || empty($password) || empty($mobiles) || $username != 'admin' || $password != 'admin') {
            return response([
                "code" => 3,
                "message" => "Username or Password is invalid.",
            ]);
        }
        $ides = [];
        for ($i = 0; $i < count($mobiles); $i++) {
            $ides[$i] = "fakesms" . uniqId();
        }
        return response([
            "ids" => $ides
        ]);
    }
}
