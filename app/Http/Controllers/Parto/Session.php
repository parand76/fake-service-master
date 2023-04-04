<?php

namespace App\Http\Controllers\Parto;

use App\Http\Controllers\Controller;
use App\Models\Session as ModelsSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Session extends Controller
{
    public function Create()
    {
        extract(request()->all());

        ///> validate the request
        $validated = Validator::make(request()->all(), [
            'OfficeId' => ['required', Rule::in(['CRS001329'])],
            'UserName' => ['required', Rule::in(['api'])],
            'Password' => ['required', Rule::in([hash("sha512", 'demo2019')])],
        ], [
            'OfficeId.required' => 'AccountNumber cannot be null,Err0102002',
            'OfficeId.in' => 'Invalid login credentials supplied,Err0102001',
            'UserName.required' => 'UserName cannot be null,Err0102003',
            'UserName.in' => 'Invalid login credentials supplied,Err0102001',
            'Password.required' => 'Password cannot be null,Err0102004',
            'Password.in' => 'Invalid login credentials supplied,Err0102001',
        ]);

        ///> if the request validation fails
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

        //right input values
        if (
            $OfficeId == 'CRS001329' && $UserName == 'api' &&
            $Password == 'aee88646f849bd6224295f5acd01a4affa50e5596e171473838f7b49681645acabe12e71486edd0d8df969d1ff8a883300aca419c69d4b616c6ee642c68a605a'
        ) {
            $sessionId = "fake-service-parto-8c8d-" . uniqid();
            $session = new ModelsSession();
            $session->sessionId = $sessionId;
            $session->seller = 'parto';
            $session->expired_at = Carbon::now()->addMinute(60);
            $session->save();

            return response([
                "Error" => null,
                "Success" => true,
                "SessionId" => $sessionId
            ], 200);
        }

        return response('Unkown', 499);
    }

    public function end()
    {
        extract(request()->all());

        ///> request validation
        $validated = Validator::make(request()->all(), [
            "SessionId" => ["required", "regex:/^(fake-service-parto-8c8d-).*\b/"],
        ], [
            "SessionId.required" => "SessionID cannot be null,Err0102007",
            "SessionId.regex" => "Invalid SessionID,Err0102008",
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
            ],422);
        }

        
        if (isset($SessionId) && preg_match('/^(fake-service-parto-8c8d-).*\b/', $SessionId) == true) {
            $session = ModelsSession::where('sessionId', $SessionId)->first();
            $session->expired_at = Carbon::now()->toDateTime();
            $session->update();
            return response([
                "Success" => true,
                "Error" => null,
            ],200);
        }
        return response('Unkown', 499);
    }
}
