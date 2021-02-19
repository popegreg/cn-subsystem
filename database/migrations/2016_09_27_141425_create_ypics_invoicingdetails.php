<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYpicsInvoicingdetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ypics_invoicingdetails', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('packinglist_id',false, true)->length(20);
            $table->string('item_no');
            $table->string('packinglist_ctrl');
            $table->string('products');
            $table->integer('sold_to_id',false, true)->length(20);
            $table->text('soldto_address');
            $table->text('shipto_address');
            $table->string('draft_shipment');
            $table->string('ship_date');
            $table->string('shippedfrom');
            $table->string('shipto');
            $table->string('carrier');
            $table->string('gross_weight');
            $table->string('terms_of_payment');
            $table->string('po_no');
            $table->string('description');
            $table->string('country_origin');
            $table->string('quantity');
            $table->string('unitprice');
            $table->string('amount');
            $table->string('revision_no');
            $table->string('transaction_no');
            $table->string('for_bir_no');
            $table->string('pickup_date');
            $table->string('invoice_date');
            $table->string('freight');
            $table->string('via');
            $table->string('sailing_on');
            $table->string('no_of_packaging');
            $table->string('awb_no');
            $table->string('prepared_by');
            $table->string('remarks');
            $table->string('note_hightlight');
            $table->string('item_code');
            $table->text('case_marks')->nullable();
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
        Schema::drop('ypics_invoicingdetails');
    }
}
