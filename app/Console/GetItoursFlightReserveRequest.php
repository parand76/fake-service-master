<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetItoursFlightReserveRequest extends Command
{
    protected $signature = 'GetItoursFlightReserveRequest';

    protected $description = '';

    public function handle()

    {
        $bookRequests = DB::table('itours')->where('message', 'like', '%Supplier Request - IToursGetFlightReserve%')->pluck('info')->toArray();
        $dataRequest = [];
        foreach ($bookRequests as $resultKey => $result) {
            $result = (json_decode($result, true));
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
