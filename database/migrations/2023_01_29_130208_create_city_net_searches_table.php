<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityNetSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_net_searches', function (Blueprint $table) {
            $table->id();
            $table->integer('AdultCount');
            $table->integer('ChildCount');
            $table->integer('InfantCount');
            $table->string('Cabin',200);
            $table->string('OriginCode',200);
            $table->string('DestinationCode',200);
            $table->string('DepartureDateTime',255);
            $table->string('TripType',200);
            $table->json('response');
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
        Schema::dropIfExists('city_net_searches');
    }
}
