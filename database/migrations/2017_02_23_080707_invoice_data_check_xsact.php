<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InvoiceDataCheckXsact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_data_check_xsact', function (Blueprint $table) {
            $table->increments('id');
            $table->string('porder')->nullable();
            $table->string('akubu')->nullable();
            $table->integer('jitu',false, true)->length(20)->nullable();
            $table->string('fdate')->nullable();
            $table->string('ftime')->nullable();
            $table->integer('aprice',false, true)->length(20)->nullable();
            $table->string('hokan')->nullable();
            $table->string('invoice_num')->nullable();
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
        Schema::drop('invoice_data_check_xsact');
    }
}
