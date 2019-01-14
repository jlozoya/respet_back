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
            $table->string('name', 60);
            $table->boolean('show_contact_information')->default(true);
            $table->text('description')->nullable();
            $table->enum('state', ['found', 'lost', 'on_adoption', 'on_sale', 'on_hold', 'other']
            )->default('found');
            $table->integer('direction_id')->unsigned()->nullable();
            $table->integer('direction_accuracy')->default(0);
            $table->integer('media_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
            ->onDelete('cascade')->onUpdate('cascade');
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
