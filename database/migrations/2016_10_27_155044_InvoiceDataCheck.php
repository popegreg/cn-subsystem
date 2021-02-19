<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InvoiceDataCheck extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_data_check', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice');
            $table->string('fdate');
            $table->string('itemcode');
            $table->string('itemname');
            $table->integer('qty',false, true)->length(20);
            $table->string('pr');
            $table->double('price',20,4);
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
        Schema::drop('invoice_data_check');
    }
}
