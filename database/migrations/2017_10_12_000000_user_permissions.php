user permissions<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->boolean('show_main_email')->default(1);
            $table->boolean('show_alternative_emails')->default(1);
            $table->boolean('show_main_phone')->default(1);
            $table->boolean('show_alternative_phones')->default(1);
            $table->boolean('show_direction')->default(1);
            $table->boolean('receive_mail_adds')->default(1);
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
        Schema::dropIfExists('user_permissions');
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
