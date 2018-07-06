<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserArchivementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_archivements', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->integer('user_id')->unsigned();
            $table->integer('archivement_id')->unsigned();
            $table->float('progress');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('archivement_id')->references('id')->on('archivements');
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
        Schema::dropIfExists('user_archivements');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
