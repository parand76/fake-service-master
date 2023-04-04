<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItoursReservePnrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itours_reserve_pnrs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('validate_id');
            $table->foreign('validate_id')
            ->references('id')->on('itours_validate_flights')
            ->onDelete('restrict')
            ->onUpdate('no action');
            $table->string('pnr');
            $table->string('provider');
            $table->string('key');
            $table->json('request');
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
        Schema::dropIfExists('itours_reserve_pnrs');
    }
}
