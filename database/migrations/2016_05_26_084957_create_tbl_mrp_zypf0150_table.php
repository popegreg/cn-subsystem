<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblMrpZypf0150Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_mrp_zypf0150', function (Blueprint $table) 
        {
            $table->increments('id');$table->string('pr');
            $table->mediumInteger('response_del_time');
            $table->string('payment_no');
            $table->string('mcode');
            $table->string('mname');
            $table->string('slip_no');
            $table->string('doc_item_no');
            $table->date('expected_del_date');
            $table->string('po_doc_no');
            $table->string('po_doc_item_no');
            $table->mediumInteger('reorder_qty');
            $table->string('unit');
            $table->string('dcode');
            $table->string('dname');
            $table->string('p_po_doc_no');
            $table->string('p_po_doc_item_no');
            $table->date('p_po_doc_date');
            $table->mediumInteger('p_po_order_qty');
            $table->mediumInteger('p_order_qty');
            $table->string('p_doc_no');
            $table->string('p_doc_item_no');
            $table->date('p_expected_del_date');
            $table->string('vendor');
            $table->string('cust_name');
            $table->string('re');
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
        Schema::drop('tbl_mrp_zypf0150');
    }
}
