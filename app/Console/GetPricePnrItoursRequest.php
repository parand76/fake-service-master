<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetPricePnrItoursRequest extends Command
{
    protected $signature = 'GetPricePnrItoursRequest';

    protected $description = '';

    public function handle()

    {
        $bookRequests = DB::table('itours')->where('message', 'like', '%Supplier Request - IToursPricePnr%')->pluck('info')->toArray();
        $dataRequest = [];
        foreach ($bookRequests as $resultKey => $result) {
            $result = (json_decode($result, true));
            dump($result);
            if(isset($result['serialize']['fields'])){
                $data=unserialize($result['serialize']['fields']);
                $dataRequest[]=$data;
            }else{

                $dataRequest[] = $result;
            }
        }
        dd($dataRequest);
    }
}
