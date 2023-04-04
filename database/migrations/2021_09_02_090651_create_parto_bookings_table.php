<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartoBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parto_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('passenger_name');
            $table->string('passenger_lastname');
            $table->unsignedBigInteger('parto_flight_search_id');
            $table->string('TktTimeLimit');
            $table->string('UniqueId');
            $table->tinyInteger('Status');
            $table->tinyInteger('Category');
            $table->foreign('parto_flight_search_id')
                ->references('id')->on('parto_flight_searches')
                ->onDelete('restrict')
                ->onUpdate('no action');
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')
                ->references('id')->on('sessions')
                ->onDelete('cascade')
                ->onUpdate('no action');
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
        Schema::dropIfExists('parto_booking');
    }
}
