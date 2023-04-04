<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmadeusNewSelectedFlightTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amadeus_new_selected_flight', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('FlightId');
            $table->bigInteger('PNRId');
            $table->string('UserId',100);
            $table->longText('SessionId');
            $table->longText('SessionToken');
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
        Schema::dropIfExists('amadeus_new_selected_flight');
    }
}
