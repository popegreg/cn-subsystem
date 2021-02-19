<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InvoiceDataCheckNonVariance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_data_check_nonvariance', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoiceno',200)->nullable();
            $table->string('fdate',200)->nullable();
            $table->string('pr',200)->nullable();
            $table->string('code',200)->nullable();
            $table->string('partname',200)->nullable();
            $table->double('unitprice',20,4)->default('0');
            $table->double('orderqty',20,4)->default('0');
            $table->double('orderbal',20,4)->default('0');
            $table->double('deliveredqty',20,4)->default('0');
            $table->double('overdelivery',20,4)->default('0');
            $table->double('neworderqty',20,4)->default('0');
            $table->double('overamount',20,4)->default('0');
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
        Schema::drop('invoice_data_check_nonvariance');
    }
}
