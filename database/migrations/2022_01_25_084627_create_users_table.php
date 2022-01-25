<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id()->comment("ID пользователя");
            $table->unsignedBigInteger("actual_place_of_residence_id")->comment("ID фактическое место жительства");
            $table->string("login")->comment("Логин");
            $table->string("password")->comment("Пароль");
            $table->string("phone")->comment("Телефон");
            $table->string("role")->comment("Роль в системе");

            //relations
            $table->foreign("id")->references("id")->on("passports")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
