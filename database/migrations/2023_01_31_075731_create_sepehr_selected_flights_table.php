<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSepehrSelectedFlightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sepehr_selected_flights', function (Blueprint $table) {
            $table->id();
            $table->longText('user');
            $table->string('FlightNumber',50);
            $table->string('FareName',50);
            $table->json('FlightSegment');
            $table->string('CurrencyCode',50);
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
        Schema::dropIfExists('sepehr_selected_flights');
    }
}
