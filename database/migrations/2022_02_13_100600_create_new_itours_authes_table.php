<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewItoursAuthesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_itours_authes', function (Blueprint $table) {
            $table->id();
            $table->string('accessToken');
            $table->string('apiKey');
            $table->string('tenantId');
            $table->string('currency');
            $table->string('username');
            $table->string('password');
            $table->string('expireInSeconds');
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
        Schema::dropIfExists('new_itours_authes');
    }
}
