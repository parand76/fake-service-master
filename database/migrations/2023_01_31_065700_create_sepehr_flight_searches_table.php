<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSepehrFlightSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sepehr_flight_searches', function (Blueprint $table) {
            $table->id();
            $table->string('OriginCode',50)->nullable();
            $table->string('DestinationCode',50)->nullable();
            $table->string('DepartureDateTime',100)->nullable();
            $table->string('CurrencyCode',50);
            $table->json('CharterFlights')->nullable();
            $table->json('WebserviceFlights')->nullable();
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
        Schema::dropIfExists('sepehr_flight_searches');
    }
}
