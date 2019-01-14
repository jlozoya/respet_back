<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CatEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cat_emails', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->integer('contact_information_id')->unsigned();
            $table->string('email', 60);
            $table->timestamps();
            
            $table->foreign('contact_information_id')->references('id')->on('contact_information')
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
        Schema::dropIfExists('cat_emails');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
