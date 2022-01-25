<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id()->comment("ID студента");
            $table->unsignedBigInteger("group_id")->comment("ID группы");
            $table->date("receipt_date")->comment("Дата поступления");

            //relations
            $table->foreign("group_id")->references("id")->on("groups");
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
        Schema::dropIfExists('students');
    }
}
