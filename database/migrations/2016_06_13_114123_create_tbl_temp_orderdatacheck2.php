<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTempOrderdatacheck2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_orderdatacheck2', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po');
            $table->string('name');
            $table->string('code');
            $table->string('qty')->nullable();
            $table->string('cdate')->nullable();
            $table->string('cust')->nullable();
            $table->string('custname')->nullable();
            $table->string('price')->nullable();
            $table->string('drawing_num')->nullable();
            $table->string('buyers')->nullable();
            $table->string('con')->nullable();
            $table->index('po');
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
