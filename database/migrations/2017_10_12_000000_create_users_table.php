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
            $table->string('name', 60);
            $table->string('first_name', 60);
            $table->string('last_name', 60);
            $table->string('gender', 6);
            $table->string('email');
            $table->string('password', 60);
            $table->string('Authorization', 60)->unique();
            $table->text('img_url')->nullable();
            $table->string('source', 8);
            $table->integer('phone_number')->nullable();
            $table->string('extern_id')->nullable();
            $table->date('birthday')->nullable();
            $table->string('contry', 60)->nullable();
            $table->string('administrative_area_level_1', 60)->nullable();
            $table->string('administrative_area_level_2', 60)->nullable();
            $table->string('route', 60)->nullable();
            $table->integer('street_number')->nullable();
            $table->boolean('confirmed')->default(false);
            $table->boolean('app_admin')->default(false);
            $table->timestamps();
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
