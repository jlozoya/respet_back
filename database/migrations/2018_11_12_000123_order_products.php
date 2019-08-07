<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->integer('order_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('amount')->unsigned()->default(1);
            $table->float('price', 8, 2)->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')
            ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product_id')->references('id')->on('products')
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
        Schema::dropIfExists('order_products');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
