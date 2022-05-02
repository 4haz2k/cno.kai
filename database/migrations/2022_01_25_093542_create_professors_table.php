<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfessorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('professors', function (Blueprint $table) {
            $table->id()->comment("ID преподавателя");
            $table->string("position")->comment("Должность");
            $table->bigInteger("personnel_number")->comment("Табельный номер");
            $table->bigInteger("ITN")->comment("ИНН");
            $table->bigInteger("INILA")->comment("СНИЛС");
            $table->string("department")->comment("Кафедра");
            $table->date("date_of_commencement_of_teaching_activity")->comment("Дата начала преподавательской деятельности");
            $table->string("description")->nullable(true)->comment("Описание");
            $table->double("price")->comment("Ставка");

            //relations
            $table->foreign("id")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('professors');
    }
}
