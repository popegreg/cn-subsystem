<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BarcodePrintMobile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barcode_print_mobile', function (Blueprint $table) {
            $table->increments('id');
            $table->datetime('printdate')->nullable();
            $table->string('txnno')->nullable();
            $table->string('txndate')->nullable();
            $table->string('itemno')->nullable();
            $table->string('itemdesc')->nullable();
            $table->double('qty',20,4)->nullable();
            $table->integer('bcodeqty',false, true)->length(20)->default(0);
            $table->string('lotno')->nullable();
            $table->string('location')->nullable();
            $table->string('barcode')->nullable();
            $table->string('printedby')->nullable();
            $table->string('trancode')->nullable();
            $table->integer('printerid',false, true)->length(20)->default(0);
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
        Schema::drop('barcode_print_mobile');
    }
}
