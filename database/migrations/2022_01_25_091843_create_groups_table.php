<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id()->comment("ID группы");
            $table->string("group_code")->comment("Код группы");
            $table->unsignedBigInteger("specialty_id")->comment("ID специальности");

            //relations
            $table->foreign("specialty_id")->references("id")->on("specialty");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
