<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InvoiceDataCheckTcalc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_data_check_tcalc', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoiceno')->nullable();
            $table->datetime('fltdate')->nullable();
            $table->string('code')->nullable();
            $table->string('partname')->nullable();
            $table->string('pr')->nullable();
            $table->double('unitprice',20,4)->nullable();
            $table->double('total_req',20,4)->nullable();
            $table->double('whs100',20,4)->nullable();
            $table->double('whs102',20,4)->nullable();
            $table->double('excess',20,4)->nullable();
            $table->integer('invqty',false, true)->length(20)->nullable();
            $table->integer('allowance',false, true)->length(20)->nullable();
            $table->integer('pr_bal',false, true)->length(20)->nullable();
            $table->integer('f1',false, true)->length(20)->nullable();
            $table->integer('f2',false, true)->length(20)->nullable();
            $table->integer('f3',false, true)->length(20)->nullable();
            $table->integer('f4',false, true)->length(20)->nullable();
            $table->integer('f5',false, true)->length(20)->nullable();
            $table->integer('count',false, true)->length(20)->nullable();
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
        Schema::drop('invoice_data_check_tcalc');
    }
}
