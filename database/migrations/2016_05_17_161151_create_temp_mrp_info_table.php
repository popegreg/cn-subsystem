<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempMrpInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_mrp_info', function (Blueprint $table) {
            $table->increments('id');
            $table->date('order_date');   // search->po date
            $table->date('due_date');     // search->demand
            $table->string('po',200);         // search->po
            $table->string('mcode',200);      // search->code
            $table->string('mname',200);      // search->name
            $table->mediumInteger('order_qty');         //search->qty
            $table->mediumInteger('order_bal');         //search->qty
            $table->string('cust_code',200);  //search->customer
            $table->string('cust_name',200);  //search->customer  
            // search->updatedby
            // search->remarks
            // r3answer->time
            $table->string('dcode',200);      // details->code
            $table->string('dname',200);      // details->name
            $table->string('vendor',200);     // details->vi
            $table->double('sched_qty',20,2);       // details->po req
            $table->double('balance_req',20,2);     // details->po balance
            $table->double('total_bal_req',20,2);   // details->gross req
            $table->double('assy100',20,2);         // details->assy100
            $table->double('assy102',20,2);  
            $table->double('whs100',20,2);          // details->whs100
            $table->double('whs102',20,2);          // details->whs102
            $table->double('whs106',20,2); 
            $table->double('whs_sm',20,2); 
            $table->double('whs_non',20,2);
            $table->double('total_curr_inv',20,2);  // details->total
            $table->mediumInteger('req_accum');        // details->inv_resr
            $table->mediumInteger('total_pr_bal');     // details->pr bal
            $table->mediumInteger('mrp');              // details->mrp
            $table->date('pr_issued');   // details->pr issued
            $table->string('pr',200);        // details->pr
            $table->string('flight',200);    // details-> pickup_gr ???
            $table->string('yec_po',200);    // details->yec po
            $table->date('yec_pu');      // details->pec pu
            $table->mediumInteger('deli_qty');         // details->pu qty ???
            $table->string('check',200);     // details->check
            $table->mediumInteger('deliaccum');    // details->deliaccum
            $table->string('sup_code');
            $table->string('sup_name',200);  // details->in charge
            $table->string('re',200);        // details->re
            $table->string('status',200);    // details->status
            $table->string('allocation_calc',200); // details->alloc calc
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
        Schema::drop('temp_mrp_info');
    }
}