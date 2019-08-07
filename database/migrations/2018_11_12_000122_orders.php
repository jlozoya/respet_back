<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Orders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->integer('user_id')->unsigned();
            $table->integer('roundsman_id')->unsigned()->nullable();
            $table->enum('state', ['on_create', 'stored', 'on_transit', 'delivered', 'other']
            )->default('on_create');
            $table->float('price', 8, 2)->nullable();
            $table->dateTime('take_out_date')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->integer('location_id')->unsigned()->nullable();
            $table->integer('destination_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
            ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('roundsman_id')->references('id')->on('users')
            ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('location_id')->references('id')->on('directions')
            ->onDelete('set null')->onUpdate('cascade');
            $table->foreign('destination_id')->references('id')->on('directions')
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
        Schema::dropIfExists('orders');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
