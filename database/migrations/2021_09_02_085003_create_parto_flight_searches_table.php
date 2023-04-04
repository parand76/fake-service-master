<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartoFlightSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parto_flight_searches', function (Blueprint $table) {
            $table->id();
            $table->integer('AdultCount');
            $table->integer('InfantCount');
            $table->integer('ChildCount');
            $table->integer('AirTripType');
            $table->string('OriginLocationCode');
            $table->string('DestinationLocationCode');
            $table->string('DepartureDateTime');
            $table->json('response');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parto_flight_search');
    }
}
