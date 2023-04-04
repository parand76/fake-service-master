<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccelaeroSelectedFlightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accelaero_selected_flights', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('password');
            $table->string('FlightId',200);
            $table->string('DepartureDateTime',200);
            $table->string('ArrivalDateTime',200);
            $table->json('OriginDestinationOption');
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
        Schema::dropIfExists('accelaero_selected_flights');
    }
}
