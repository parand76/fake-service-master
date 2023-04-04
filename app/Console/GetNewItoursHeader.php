<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetNewItoursHeader extends Command
{
    protected $signature = 'GetNewItoursHeader';

    protected $description = '';

    public function handle()

    {
        $bookRequests = DB::table('itours_v2')->where('message', 'like', '%Supplier Request%')->pluck('info')->toArray();
        $dataRequest = [];
        foreach ($bookRequests as $resultKey => $result) {
            $result =(json_decode($result, true));
            dd($result);
            $data=unserialize($result['serialize']['curlResponse']);
            dd($data['headers']);
            $request =json_decode($data['headers'], true);
            $dataRequest[]=$request;
        }
        dd($dataRequest);
    }
}
