<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTboSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbo_searches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_tbo_result_id');
            $table->foreign('sample_tbo_result_id')
                ->references('id')->on('sample_tbo_results')
                ->onDelete('restrict')
                ->onUpdate('no action');
            $table->string('room_count');
            $table->string('adult_count');
            $table->string('child_count')->nullable();
            $table->string('infant_count')->nullable();
            $table->string('nationality');
            $table->string('checkin_date');
            $table->string('checkOut_date');
            $table->string('sessionId');
            $table->string('cityId');
            $table->json('hotel');
            $table->timestamp('expired_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbo_searches');
    }
}
