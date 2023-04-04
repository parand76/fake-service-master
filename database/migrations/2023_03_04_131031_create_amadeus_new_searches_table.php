<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmadeusNewSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amadeus_new_searches', function (Blueprint $table) {
            $table->id();
            $table->integer('ADT');
            $table->integer('CHD');
            $table->integer('INF');
            $table->string('OriginLocation',100);
            $table->string('DestinationLocation',100);
            $table->string('DepratureDate',100);
            $table->string('ReturnDate',100);
            $table->json('Itinerary');
            $table->json('Recommendation');
            $table->integer('NumberOfRec');
            $table->json('Response_json');
            $table->longText('Response_XML');
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
        Schema::dropIfExists('amadeus_new_searches');
    }
}
