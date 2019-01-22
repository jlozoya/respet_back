<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Users extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('name', 60)->index();
            $table->string('first_name', 60);
            $table->string('last_name', 60);
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('email', 60)->index();
            $table->string('password')->nullable();
            $table->integer('media_id')->unsigned()->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('lang', 5)->default('es');
            $table->date('birthday')->nullable();
            $table->boolean('confirmed')->default(false);
            $table->enum('grant_type',
                ['password', 'google', 'facebook', 'instagram', 'twitter', 'other']
            )->default('grant_type');
            $table->enum('role', ['visitor', 'user', 'admin', 'other'])
            ->default('visitor');
            $table->integer('direction_id')->unsigned()->nullable();
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
        Schema::dropIfExists('users');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
