<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->id()->comment("ID тарифа");
            $table->unsignedBigInteger("service_id")->comment("ID услуги");
            $table->unsignedBigInteger("position_id")->comment("ID должности");
            $table->double("price")->comment("Стоимость");

            //relations
            $table->foreign("service_id")->references("id")->on("services");
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
        Schema::dropIfExists('rates');
    }
}
