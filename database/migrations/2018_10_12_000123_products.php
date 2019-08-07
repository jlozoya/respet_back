<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Products extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->text('name');
            $table->text('description');
            $table->integer('amount');
            $table->float('price', 8, 2);
            $table->integer('warehouse_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')
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
        Schema::dropIfExists('products');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
