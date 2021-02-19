<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TempPrbInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_prb_invoice', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no')->nullable();
            $table->datetime('flightdate')->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->integer('invqty',false,true)->length(50)->nullable();
            $table->string('podata')->nullable();
            $table->string('sunitprice')->nullable();
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
        Schema::drop('temp_prb_invoice');
    }
}
