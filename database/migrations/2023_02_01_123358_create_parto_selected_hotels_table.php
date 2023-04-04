<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartoSelectedHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parto_selected_hotels', function (Blueprint $table) {
            $table->id();
            $table->longText('SessionId');
            $table->string('HotelId',200);
            $table->string('FareSourceCode',255);
            $table->json('HotelDetails');
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
        Schema::dropIfExists('parto_selected_hotels');
    }
}
