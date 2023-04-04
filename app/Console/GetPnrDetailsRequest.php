<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetPnrDetailsRequest extends Command
{
    protected $signature = 'GetPnrDetailsRequest';

    protected $description = '';

    public function handle()

    {
        $bookRequests = DB::table('itours')->where('message', 'like', '%Supplier Request - IToursGetPNRDetails%')->pluck('info')->toArray();
        dd($bookRequests);
        $dataRequest = [];
        foreach ($bookRequests as $resultKey => $result) {
            $result = (json_decode($result, true));
            dump($result);
            if(isset($result['serialize']['fields'])){
                $data=unserialize($result['serialize']['fields']);
                dd($data);
                $dataRequest[]=$data;
            }else{

                $dataRequest[] = $result;
            }
        }
        dd($dataRequest);
    }
}
