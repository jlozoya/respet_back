<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class HealthStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('health_stats', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->integer('pet_id')->unsigned();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('pet_id')->references('id')->on('pets')
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
        Schema::dropIfExists('health_stats');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
