<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TSMRP extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::create('ts_mrp', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mcode')->nullable();
            $table->string('mname')->nullable();
            $table->string('vendor')->nullable();
            $table->string('assy100')->nullable();
            $table->string('assy102')->nullable();
            $table->string('whs100')->nullable();
            $table->string('whs102')->nullable();
            $table->string('whs106')->nullable();
            $table->string('whs_sm')->nullable();
            $table->string('whs_non')->nullable();
            $table->string('ttlcurrinvtry')->nullable();
            $table->string('orddate')->nullable();
            $table->string('duedate')->nullable();
            $table->string('po')->nullable();
            $table->string('dcode')->nullable();
            $table->string('dname')->nullable();
            $table->string('orderqty')->nullable();
            $table->string('orderbal')->nullable();
            $table->string('custcode')->nullable();
            $table->string('custname')->nullable();
            $table->string('schdqty')->nullable();
            $table->string('balreq')->nullable();
            $table->string('ttlbalreq')->nullable();
            $table->string('reqaccum')->nullable();
            $table->string('alloccalc')->nullable();
            $table->string('ttlpr_bal')->nullable();
            $table->string('mrp')->nullable();
            $table->string('pr_issued')->nullable();
            $table->string('pr')->nullable();
            $table->string('yec_po')->nullable();
            $table->string('yec_pu')->nullable();
            $table->string('flight')->nullable();
            $table->string('deliqty')->nullable();
            $table->string('deliaccum')->nullable();
            $table->string('check')->nullable();
            $table->string('supcode')->nullable();
            $table->string('supname')->nullable();
            $table->string('re')->nullable();
            $table->string('status')->nullable();
            $table->string('isDeleted')->nullable();
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
        Schema::drop('ts_mrp');
    }
}
