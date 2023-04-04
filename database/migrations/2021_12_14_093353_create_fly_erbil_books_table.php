<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlyErbilBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fly_erbil_books', function (Blueprint $table) {
            $table->id();
            $table->string('pnr_code');
            $table->string('timelimitTicket');
            $table->string('access_token');
            $table->json('price_info');
            $table->json('passenger_info');

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
        Schema::dropIfExists('fly_erbil_books');
    }
}
