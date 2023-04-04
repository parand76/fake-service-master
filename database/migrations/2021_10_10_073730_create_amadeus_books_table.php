<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmadeusBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amadeus_books', function (Blueprint $table) {
            $table->id();
            $table->string('contexId');
            $table->timestamp('timeTiket');
            $table->json('response');
            $table->unsignedBigInteger('amadeus_search_id');
            $table->foreign('amadeus_search_id')
            ->references('id')->on('amadeus_searches')
            ->onDelete('restrict')
            ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('amadeus_books');
    }
}
