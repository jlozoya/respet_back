<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Bulletins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulletins', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->text('title');
            $table->text('description');
            $table->date('date');
            $table->integer('media_id')->unsigned()->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('bulletins');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
