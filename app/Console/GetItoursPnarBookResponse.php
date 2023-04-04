<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetItoursPnarBookResponse extends Command
{
    protected $signature = 'GetItoursPnarBookResponse';

    protected $description = '';

    public function handle()
    
    {
        $bookResults = DB::table('itours')->where('message', 'like', '%Supplier Response - IToursBook%')->pluck('info')->toArray();
        $data = [];
        foreach ($bookResults as $resultKey => $result) {
            $data[] = json_decode($result, true);
            $result = [];
            foreach ($data as $item) {
                if (isset($item['serialize']['fields'])) {
                    $item = unserialize($item['serialize']['fields']);
                    $result[] = (json_decode($item['fields'], true));
                } else {
                    $result = $data;
                }
            }
        }
        $answers = [];
        foreach ($result as $res) {
            if (!empty($res['serialize']['curlResponse'])) {
                $answers[] = (unserialize($res['serialize']['curlResponse']));
            }
        }
        $body = [];
        $successResponse = [];
        $errorResponse = [];
        foreach ($answers as $key => $answer) {
            $body[] = json_decode($answers[0]['body'], true);
        }
        foreach ($body as $boyItem) {
            if ($boyItem['success'] == true) {
                $successResponse[] = $boyItem;
            } else {
                $errorResponse[] = $boyItem;
            }
        }
        dd($errorResponse, $successResponse);
    }
}
