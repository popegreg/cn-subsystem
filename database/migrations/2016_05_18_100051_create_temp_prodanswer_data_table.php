<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempProdanswerDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_prodanswer_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pcode',200);
            $table->string('pname',200);
            $table->string('po',200);
            $table->mediumInteger('qty');
            $table->date('r3answer');
            $table->mediumInteger('time');
            $table->string('re',200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('temp_prodanswer_data');
    }
}
