<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYPICSInvoicingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ypics_invoicings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_no');
            $table->string('transaction_no');
            $table->string('packinglist_ctrl');
            $table->string('customer');
            $table->string('description_of_goods');
            $table->string('quantity');
            $table->string('amount');
            $table->string('destination');
            $table->string('invoice_date');
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
        Schema::drop('ypics_invoicings');
    }
}
