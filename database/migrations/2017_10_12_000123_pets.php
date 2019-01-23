<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Pets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->integer('user_id')->unsigned();
            $table->text('description');
            $table->enum('state', ['found', 'lost', 'on_adoption', 'on_sale', 'on_hold', 'other']
            )->default('found');
            $table->integer('direction_id')->unsigned()->nullable();
            $table->integer('direction_accuracy')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
            ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('direction_id')->references('id')->on('directions')
            ->onDelete('set null')->onUpdate('cascade');
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
        Schema::dropIfExists('pets');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
