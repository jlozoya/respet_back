<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
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
            $table->string('password', 60);
            $table->string('Authorization', 60)->unique()->index();
            $table->text('img_url')->nullable();
            $table->enum('source', ['app', 'facebook', 'google', 'other']);
            $table->string('phone', 15)->nullable();
            $table->string('extern_id')->nullable();
            $table->date('birthday')->nullable();
            $table->boolean('confirmed')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->integer('direction_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('direction_id')->references('id')->on('directions')->onDelete('cascade')->onUpdate('cascade');
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
