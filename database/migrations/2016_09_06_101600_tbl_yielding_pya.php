<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblYieldingPya extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_yielding_pya', function (Blueprint $table) {
            $table->increments('id');
            $table->string('yieldingno');
            $table->string('pono');
            $table->date('productiondate');
            $table->string('yieldingstation');
            $table->string('accumulatedoutput');
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
        //
    }
}
