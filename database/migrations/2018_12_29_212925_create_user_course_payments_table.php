<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCoursePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_course_payments', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->integer('user_id')->unsigned();
            $table->integer('course_price_id')->unsigned();
            $table->integer('amount')->unsigned();
            $table->string('description')->nullable();
            $table->string('charge_id', 60);
            $table->string('status', 20);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('course_price_id')->references('id')->on('course_prices');
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
        Schema::dropIfExists('user_course_payments');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}