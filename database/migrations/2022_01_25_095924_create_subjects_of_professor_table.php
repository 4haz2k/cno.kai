<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsOfProfessorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects_of_professor', function (Blueprint $table) {
            $table->id()->comment("ID предмета преподавателя");
            $table->unsignedBigInteger("subject_id")->comment("ID предмета");
            $table->unsignedBigInteger("professor_id")->comment("ID преподавателя");

            //relations
            $table->foreign("subject_id")->references("id")->on("subjects");
            $table->foreign("professor_id")->references("id")->on("professors");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subjects_of_professor');
    }
}
