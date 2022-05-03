<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id()->comment("ID адреса");
            $table->string("country")->comment("Страна");
            $table->string("region")->nullable(true)->comment("Область/край/республика");
            $table->string("locality")->comment("Населенный пункт");
            $table->string("district")->nullable(true)->comment("Район");
            $table->string("street")->comment("Улица");
            $table->string("house")->comment("Дом");
            $table->string("frame")->nullable(true)->comment("Корпус");
            $table->string("apartment")->nullable(true)->comment("Квартира");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
