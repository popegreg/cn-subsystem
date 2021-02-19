<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrbOutputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prb_outputs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pr')->nullable();
            $table->string('mcode')->length(20)->nullable();
            $table->string('mname',100)->nullable();
            $table->string('supplier',100)->nullable();
            $table->integer('whs100',false, true)->length(10)->nullable();
            $table->integer('whs102',false, true)->length(10)->nullable();
            $table->integer('assy100',false, true)->length(10)->nullable();
            $table->integer('assy102',false, true)->length(10)->nullable();
            $table->integer('whsnon',false, true)->length(10)->nullable();
            $table->integer('whssm',false, true)->length(10)->nullable();
            $table->integer('total',false, true)->length(10)->nullable();
            $table->string('orderissuedate',10)->nullable();
            $table->integer('podqty',false, true)->length(10)->nullable();
            $table->integer('pprbal',false, true)->length(10)->nullable();
            $table->integer('yecqty',false, true)->length(10);
            $table->integer('invoiceqty',false, true)->length(10)->nullable();
            $table->integer('difference',false, true)->length(10)->nullable();
            $table->integer('currentinvntry',false, true)->length(10)->nullable();
            $table->integer('requirement',false, true)->length(10)->nullable();
            $table->string('chk')->nullable();
            $table->string('remarks')->nullable();
            $table->enum('locked',['0','1'])->nullable();
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
        Schema::drop('prb_outputs');
    }
}
