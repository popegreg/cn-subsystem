<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InvoiceDataCheckXslip extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_data_check_xslip', function (Blueprint $table) {
            $table->increments('id');
            $table->string('orderno')->nullable();
            $table->string('itemcode')->nullable();
            $table->string('suppliername')->nullable();
            $table->integer('schdqty',false, true)->length(20)->nullable();
            $table->integer('actualqty',false, true)->length(20)->nullable();
            $table->integer('availqty',false, true)->length(20)->nullable();
            $table->string('remarks')->nullable();
            $table->datetime('correctdate')->nullable();
            $table->string('correctuser')->nullable();
            $table->string('ddate')->nullable();
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
        Schema::drop('invoice_data_check_xslip');
    }
}
