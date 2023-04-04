<?php

namespace App\Console;

use App\Models\PartoHotelBooking;
use App\Models\SampleTboResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Faker;
class GetPartoHotelBooking extends Command
{
    protected $signature = 'GetPartoHotelBooking';

    protected $description = '';


    public function handle()
    {
        $faker = Faker\Factory::create();
        $response = DB::table('logs')->where('message', 'Supplier Response - PartoBookAndIssueHotel - Parto Hotel')->get();
        $insert = [];
        foreach ($response as $res) {

            $item = unserialize(json_decode($res->info, true)['serialize']['curlResponse']);
            $body = $item['body'];
            $array = json_decode($body, true);

            $PaymentDeadline = $array['PaymentDeadline'];
            $UniqueId = $array['UniqueId'];
            $Category = $array['Category'];
            $Status = $array['Status'];
            $PhoneNumber = $faker->phoneNumber;
            $Email = $faker->email;
            $Rooms = [
                  "Passengers" => [
                      "FirstName" => $faker->firstName,
                      "LastName" => $faker->lastName,
                      "PassengerType" => 1
                  ]
            ];
            $insert[] = [
                'FareSourceCode' => null,
                'PhoneNumber' => $PhoneNumber,
                'Email' => $Email,
                'Rooms' => json_encode($Rooms),
                'UniqueId' => $UniqueId,
                'PaymentDeadline' => $PaymentDeadline,
                'Status' => $Status,
                'Category' =>  $Category, 
                'responses' => $body
            ];
        }

        $this->info('start inserting : ' . count($insert) . ' items');
        foreach (array_chunk($insert, 10) as $flight) {
            PartoHotelBooking::insert($flight);
            $this->info('done');
        }
    }
}
