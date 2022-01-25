<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->comment("ID заказа");
            $table->unsignedBigInteger("student_id")->comment("ID студента");
            $table->unsignedBigInteger("timetable_id")->comment("ID расписания");
            $table->unsignedBigInteger("service_id")->comment("ID услуги");
            $table->string("status")->comment("Статус");
            $table->double("price")->comment("Стоимость");
            $table->string("treaty")->comment("Договор");
            $table->dateTime("create_date")->comment("Дата создания");
            $table->integer("number_of_lessons")->comment("Кол-во занятий");

            //relations
            $table->foreign("student_id")->references("id")->on("students");
            $table->foreign("timetable_id")->references("id")->on("timetable");
            $table->foreign("service_id")->references("id")->on("services");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
