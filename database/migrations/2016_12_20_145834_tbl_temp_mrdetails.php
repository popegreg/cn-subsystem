<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblTempMrdetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_temp_mrdetails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoiceno');
            $table->string('item');
            $table->string('description');
            $table->double('qty',20,4);
            $table->string('pr');
            $table->double('price',20,4);
            $table->double('amount',20,4);
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
        Schema::drop('tbl_temp_mrdetails');
    }
}
