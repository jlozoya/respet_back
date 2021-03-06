<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class PasswordsResets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passwords_resets', function (Blueprint $table) {
            $table->string('email');
            $table->string('token', 120)->index();
            $table->timestamp('created_at');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('passwords_resets');
    }
}