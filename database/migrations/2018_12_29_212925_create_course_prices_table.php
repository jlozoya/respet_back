<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_prices', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('title', 60);
            $table->integer('course_id')->unsigned();
            $table->integer('amount')->unsigned();
            $table->string('duration', 60);
            $table->text('includes')->nullable();
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses');
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
        Schema::dropIfExists('course_prices');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
