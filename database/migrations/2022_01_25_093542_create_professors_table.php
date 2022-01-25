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
            $table->unsignedBigInteger("position_id")->comment("ID должности");
            $table->integer("personnel_number")->comment("Табельный номер");
            $table->integer("ITN")->comment("ИНН");
            $table->integer("INILA")->comment("СНИЛС");
            $table->string("department")->comment("Кафедра");
            $table->date("date_of_commencement_of_teaching_activity")->comment("Дата начала преподавательской деятельности");
            $table->string("description")->nullable(true)->comment("Описание");

            //relations
            $table->foreign("id")->references("id")->on("users");
            $table->foreign("position_id")->references("id")->on("positions");
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
