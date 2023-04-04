<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTboSelectedRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbo_selected_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('selectedRooms_id');
            $table->unsignedBigInteger('tbo_search_id');
            $table->foreign('tbo_search_id')
            ->references('id')->on('tbo_searches')
            ->onDelete('restrict')
            ->onUpdate('no action');
            $table->json('hotel_info');
            $table->boolean('moltyRoom')->default(false);
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
        Schema::dropIfExists('tbo_selected_rooms');
    }
}
