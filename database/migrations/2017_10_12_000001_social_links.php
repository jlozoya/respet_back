<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SocialLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_links', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->integer('user_id')->unsigned();
            $table->integer('extern_id')->unsigned();
            $table->enum('source', ['google', 'facebook', 'instagram', 'twitter', 'other']);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
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
        Schema::dropIfExists('social_links');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
