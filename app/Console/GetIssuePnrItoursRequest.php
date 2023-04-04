<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;



class GetIssuePnrItoursRequest extends Command
{
    protected $signature = 'GetIssuePnrItoursRequest';

    protected $description = '';

    public function handle()

    {
        $bookRequests = DB::table('itours')->where('message', 'like', '%Supplier Request - IToursIssue%')->pluck('info')->toArray();
        $dataRequest = [];
        foreach ($bookRequests as $resultKey => $result) {
            $result = (json_decode($result, true));
            dump($result);
            if(isset($result['serialize']['fields'])){
                $data=unserialize($result['serialize']['fields']);
                // dd( $data);
                $dataRequest[]=$data;
            }else{

                $dataRequest[] = $result;
            }
        }
        dd($dataRequest);
    }
}


