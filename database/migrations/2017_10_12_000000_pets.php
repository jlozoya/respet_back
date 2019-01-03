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
            $table->string('name', 60);
            $table->text('description');
            $table->enum('state', ['found', 'lost', 'on_adoption', 'on_sale', 'on_hold', 'other']);
            $table->integer('direction_id')->unsigned()->nullable();
            $table->integer('media_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('direction_id')->references('id')->on('directions')
            ->onDelete('set null')->onUpdate('cascade');
            $table->foreign('media_id')->references('id')->on('media')
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
