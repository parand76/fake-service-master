<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityNetFlightsRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_net_flights_rules', function (Blueprint $table) {
            $table->id();
            $table->string('PassengerType',255);
            $table->string('DepartureLocationCode',255)->nullable();
            $table->string('ArrivalLocationCode',255)->nullable();
            $table->string('AirLine',255)->nullable();
            $table->string('MarketAirLine',255)->nullable();
            $table->json('response');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_net_flights_rules');
    }
}
