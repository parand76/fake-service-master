<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartoHotelBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parto_hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('FareSourceCode')->nullable();
            $table->string('PhoneNumber');
            $table->string('Email');
            $table->json('Rooms');
            $table->string('UniqueId')->nullable();
            $table->string('PaymentDeadline');
            $table->tinyInteger('Status')->nullable();
            $table->tinyInteger('Category')->nullable();
            $table->json('responses');
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
        Schema::dropIfExists('parto_hotel_bookings');
    }
}
