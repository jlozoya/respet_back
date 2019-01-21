<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PetMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_media', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->integer('pet_id')->unsigned();
            $table->integer('media_id')->unsigned();
            $table->timestamps();

            $table->foreign('pet_id')->references('id')->on('pets')
            ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('media_id')->references('id')->on('media')
            ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('pet_media');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
