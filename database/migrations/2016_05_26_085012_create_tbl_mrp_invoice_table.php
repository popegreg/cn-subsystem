<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblMrpInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_mrp_invoice', function (Blueprint $table) 
        {
            $table->increments('id');
            $table->string('no');
            $table->datetime('flight');
            $table->string('pcode');
            $table->string('pname');
            $table->mediumInteger('qty');
            $table->string('podata');
            $table->float('unit_price');
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
        Schema::drop('tbl_mrp_invoice');
    }
}
