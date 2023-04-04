<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityNetFlightsBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_net_flights_bookings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('SessionId');
            $table->bigInteger('FlightId');
            $table->json('FlightAirItinerary');
            $table->json('FlightPriceInfo');
            $table->json('TravelerInfo');
            $table->longText('BookId');
            $table->string('ContractNo',255);
            $table->string('pnr',100);
            $table->string('Currency',100);
            $table->string('TicketTimeLimit',255);
            $table->json('Ticket')->nullable();
            $table->json('Report')->nullable();
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
        Schema::dropIfExists('city_net_flights_bookings');
    }
}
