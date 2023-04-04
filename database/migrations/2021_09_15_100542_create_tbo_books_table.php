<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTboBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbo_books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tbo_selected_available_id');
            $table->foreign('tbo_selected_available_id')
                ->references('id')->on('tbo_availables')
                ->onDelete('restrict')
                ->onUpdate('no action');
            $table->string('code');
            $table->timestamp('expierd_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbo_books');
    }
}
