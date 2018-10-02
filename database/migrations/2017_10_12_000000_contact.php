<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Contact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('name', 60);
            $table->string('phone', 15)->nullable();
            $table->string('email', 60);
            $table->string('message', 255);
            $table->string('lang', 5)->default('es');
            $table->timestamps();

            $table->index('email');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact');
    }
}
