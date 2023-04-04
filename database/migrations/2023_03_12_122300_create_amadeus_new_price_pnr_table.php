<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmadeusNewPricePnrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amadeus_new_price_pnr', function (Blueprint $table) {
            $table->id();
            $table->integer('ADT');
            $table->integer('CHD');
            $table->integer('INF');
            $table->string('Origin',100);
            $table->string('Destination',100);
            $table->string('DepratureDate',100);
            $table->string('ReturnDate',100);
            $table->longText('Response_XML');
            $table->json('Response_Json');
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
        Schema::dropIfExists('amadeus_new_price_pnr');
    }
}
