<?php

namespace App\Console;

use App\Models\PartoFlightSearch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Temp extends Command
{
    protected $signature = 'temp';

    protected $description = '';

    public function handle()
    {
        // $folderPath = storage_path("searchResult/partoFlight/oneWay/DXBBGW100.json");
        // $response=file_get_contents($folderPath);

        // $search=new PartoFlightSearch();
        // $search->AdultCount=1;
        // $search->InfantCount=0;
        // $search->ChildCount=0;
        // $search->AirTripType=1;
        // $search->OriginLocationCode='DXB';
        // $search->DestinationLocationCode='BGW';
        // $search->DepartureDateTime='2021-12-15T07:05:00';
        // $search->response=$response;
        // $search->save();

        $flight=PartoFlightSearch::find(1);
       $all=json_decode($flight->response,true);
    //    dd($all['PricedItineraries']);
       foreach($all['PricedItineraries'] as $key=>$b){
           dd($b['FareSourceCode']);
       }
        // Schema::table('parto_bookings', function (Blueprint $table) {
        //     $table->tinyInteger('Status');
        // });
    }
    
}
