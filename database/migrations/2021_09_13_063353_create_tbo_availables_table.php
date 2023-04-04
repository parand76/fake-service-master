<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTboAvailablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbo_availables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tbo_search_id');
            $table->foreign('tbo_search_id')
            ->references('id')->on('tbo_searches')
            ->onDelete('restrict')
            ->onUpdate('no action');
            $table->json('hotel_info');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbo_availables');
    }
}
