<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccelaeroSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accelaero_searches', function (Blueprint $table) {
            $table->id();
            $table->integer('sample_accel_result_id');
            $table->integer('AdultCount');
            $table->integer('ChildCount')->nullable();
            $table->integer('InfantCount')->nullable();
            $table->string('TripType');
            $table->string('RPH');
            $table->string('ArrivalDateTime');
            $table->string('DepratureDateTime');
            $table->string('OriginCode');
            $table->string('DestinationCode');
            $table->json('OriginDestination');
            $table->longText('responses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accelaero_searches');
    }
}
