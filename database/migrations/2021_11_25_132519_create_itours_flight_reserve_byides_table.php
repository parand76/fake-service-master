<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItoursFlightReserveByidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itours_flight_reserve_byides', function (Blueprint $table) {
            $table->id();
            $table->string('pnrCode');
            $table->string('providerName');
            $table->unsignedBigInteger('reserve_id');
            $table->foreign('reserve_id')
            ->references('id')->on('itours_reserve_pnrs')
            ->onDelete('restrict')
            ->onUpdate('no action');
            $table->json('flight_detail');
            $table->string('username');
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
        Schema::dropIfExists('itours_flight_reserve_byides');
    }
}
