<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passports', function (Blueprint $table) {
            $table->id()->comment("ID паспорта");
            $table->integer("series")->comment("Серия");
            $table->integer("number")->comment("Номер");
            $table->date("date_of_issue")->comment("Дата выдачи");
            $table->string("issued")->comment("Выдан");
            $table->integer("division_code")->comment("Код подразделения");
            $table->string("scan")->comment("Скан паспорта");
            $table->unsignedBigInteger("place_of_residence_id")->comment("ID Адрес прописки");
            $table->string("secondname")->comment("Фамилия");
            $table->string("firstname")->comment("Имя");
            $table->string("thirdname")->nullable(true)->comment("Отчество");
            $table->date("birthday")->comment("Дата рождения");
            $table->string("sex")->comment("Пол");

            //relations
            $table->foreign("place_of_residence_id")->references("id")->on("addresses");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('passports');
    }
}
