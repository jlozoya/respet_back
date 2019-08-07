<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_media', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->integer('product_id')->unsigned();
            $table->integer('media_id')->unsigned();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')
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
        Schema::dropIfExists('product_media');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
