<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlyBaghdadBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fly_baghdad_books', function (Blueprint $table) {
            $table->id();
            $table->string('pnr');
            $table->string('ticketLimit');
            $table->json('pass_info');
            $table->json('fly_info');
            $table->json('pricing');
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
        Schema::dropIfExists('fly_baghdad_books');
    }
}
