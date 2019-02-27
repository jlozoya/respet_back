<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Addresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('country', 60)->nullable();
            $table->string('administrative_area_level_1', 60)->nullable();
            $table->string('administrative_area_level_2', 60)->nullable();
            $table->string('route', 60)->nullable();
            $table->string('street_number', 15)->nullable();
            $table->integer('postal_code')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
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
        Schema::dropIfExists('addresses');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
