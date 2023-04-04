<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSepehrFlightBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sepehr_flight_bookings', function (Blueprint $table) {
            $table->id();
            $table->longText('user');
            $table->string('FlightNumber',50);
            $table->string('FareName',50);
            $table->string('DepartureDateTime',100);
            $table->integer('AdultCount');
            $table->integer('ChildCount');
            $table->integer('InfantCount');
            $table->json('Passengers');
            $table->string('Pnr',50);
            $table->string('TotalFare',50);
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
        Schema::dropIfExists('sepehr_flight_bookings');
    }
}
