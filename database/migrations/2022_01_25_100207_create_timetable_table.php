<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimetableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timetable', function (Blueprint $table) {
            $table->id()->comment("ID расписания");
            $table->unsignedBigInteger("subject_of_professor_id")->comment("ID предмета преподавателя");
            $table->date("date")->comment("Дата");
            $table->string("classroom")->comment("Аудитория");

            //relations
            $table->foreign("subject_of_professor_id")->references("id")->on("subjects_of_professor");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timetable');
    }
}
