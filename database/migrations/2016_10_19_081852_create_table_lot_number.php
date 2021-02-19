<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLotNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lot_number_qcmolding', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po');
            $table->string('lot_no');
            $table->integer('qty',false, true)->length(20);
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
        Schema::drop('lot_number_qcmolding');
    }
}
