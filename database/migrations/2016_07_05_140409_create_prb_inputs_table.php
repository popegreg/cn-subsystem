<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrbInputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prb_inputs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pr')->nullable();
            $table->date('ans_del_date')->nullable();
            $table->double('payment',10,4)->nullable();
            $table->string('code')->length(20)->nullable();
            $table->string('name')->nullable();
            $table->string('sales_slip_num')->nullable();
            $table->string('sales_item_num')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('po_num')->nullable();
            $table->string('po_item_num')->nullable();
            $table->double('backorderqty',10,4)->nullable();
            $table->string('unit',2)->nullable();
            $table->string('item_code_prod')->nullable();
            $table->string('item_name_prod')->nullable();
            $table->string('po_num_prod')->nullable();
            $table->string('po_item_num_prod')->nullable();
            $table->string('po_date_prod')->nullable();
            $table->string('po_qty_prod')->nullable();
            $table->string('order_unit')->nullable();
            $table->string('sales_num_prod')->nullable();
            $table->string('sales_item_num_prod')->nullable();
            $table->date('cus_date')->nullable();
            $table->string('vendor')->nullable();
            $table->string('compname')->nullable();
            $table->string('delay_reason')->nullable();
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
        Schema::drop('prb_inputs');
    }
}
