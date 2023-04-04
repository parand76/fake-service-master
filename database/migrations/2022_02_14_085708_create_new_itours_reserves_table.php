<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewItoursReservesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_itours_reserves', function (Blueprint $table) {
            $table->id();
            $table->string('pnr');
            $table->string('key_validate');
            $table->string('reserved_id');
            $table->string('expiration_time');
            $table->string('trip_type');
            $table->string('booked_dateTime');
            $table->string('extra_baggage');
            $table->json('reserver');
            $table->json('passengers_info');
            $table->json('flight_detail');
            $table->json('passengers_baseFare');
            $table->json('pricing');

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
        Schema::dropIfExists('new_itours_reserves');
    }
}
