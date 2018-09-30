<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Beats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beats', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->integer('reading_id')->unsigned();
            $table->time('time');
            $table->integer('beat');
            $table->timestamps();

            $table->foreign('reading_id')->references('id')->on('readings')->onDelete('cascade');
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
        Schema::dropIfExists('beats');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
