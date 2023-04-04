<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetItoursPnrReserveRequests extends Command
{
    protected $signature = 'GetItoursPnrReserveRequests';

    protected $description = '';

    public function handle()

    {
        $bookRequests = DB::table('itours')->where('message', 'like', '%Supplier Request - IToursBook%')->pluck('info')->toArray();
        $dataRequest = [];
        foreach ($bookRequests as $resultKey => $result) {
            $result =(json_decode($result, true));
            $data=unserialize($result['serialize']['fields']);
            $request =json_decode($data['fields'], true);
            $dataRequest[]=$request;
        }
        dd($dataRequest);
    }
}
