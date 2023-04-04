<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmadeusNewFareRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amadeus_new_fare_rules', function (Blueprint $table) {
            $table->id();
            $table->string('RefrenceType',100);
            $table->integer('UniqueRefrence');
            $table->string('StatusCode',100);
            $table->string('Origin',100);
            $table->string('Destination',100);
            $table->string('PassengersCount',100);
            $table->string('HasReturn',100);
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
        Schema::dropIfExists('amadeus_new_fare_rules');
    }
}
