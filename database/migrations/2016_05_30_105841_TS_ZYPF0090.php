<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TSZYPF0090 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
 public function up()
    {
        Schema::create('ts_zypf0090', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_code');
            $table->string('item_text',200);
            $table->string('product_purchase_order',200);
            $table->string('item_number',200);
            $table->string('purchase_order_quantity',200);
            $table->string('statistical_delivery_date',200);
            $table->string('purchasing_delivery_date',200);
            $table->string('current_answer_time',200);
            $table->string('sales_order',200);
            $table->string('sales_order_specification',200);
            $table->string('proposed_response_date',200);
            $table->string('proposed_answer_time',200);
            $table->string('answer_quantity',200);
            $table->string('supplier_sector',200);
            $table->string('mrp_administrator',200);
            $table->string('issuing_storage_location',200);
            $table->string('planned_order_number',200);
            $table->string('production_orders',200);
            $table->string('purchase_order_number',200);
            $table->string('specification',200);
            $table->string('required_date',200);
            $table->string('proposed_division',200);
            $table->string('last_proposed_change_classification',200);
            $table->string('inventory_provisions_have_classification',200);
            $table->string('lock_change_classification',200);
            $table->string('vendor_code',200);
            $table->string('complete_po')->nullable();
            $table->string('isDeleted',200);
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
        Schema::drop('ts_zypf0090');
    }
}
