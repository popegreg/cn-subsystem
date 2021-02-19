<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TempZYMR0120 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tempzymr0120', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mrp_manager')->nullable();
            $table->string('purchasing_group')->nullable();
            $table->string('order_date')->nullable();
            $table->string('vendor')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('itemcode')->nullable();
            $table->string('itemname')->nullable();
            $table->string('po')->nullable();
            $table->string('itemno')->nullable();
            $table->string('drawing_num')->nullable();
            $table->string('unit')->nullable();
            $table->string('qty')->nullable();
            $table->string('num_of_residuals')->nullable();
            $table->string('currency')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('order_amount')->nullable();
            $table->string('order_the_remaining_amount_of_money')->nullable();
            $table->string('specify_period')->nullable();
            $table->string('first_sector')->nullable();
            $table->string('ans_satisfied_period')->nullable();
            $table->string('answer_time')->nullable();
            $table->string('num_response')->nullable();
            $table->string('library')->nullable();
            $table->string('reason')->nullable();
            $table->string('loan')->nullable();
            $table->string('project')->nullable();
            $table->string('text')->nullable();
            $table->string('asset_num')->nullable();
            $table->string('asset_aux_num')->nullable();
            $table->string('supplied_prod_code')->nullable();
            $table->string('payment_good_text')->nullable();
            $table->string('surface_designation')->nullable();
            $table->string('configuration')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('company_name')->nullable();
            $table->string('order_approval_date')->nullable();
            $table->string('order_approval_time')->nullable();
            $table->string('answer_force_to_pay')->nullable();
            $table->string('answer_force_moment')->nullable();
            $table->string('policy_group')->nullable();
            $table->string('shipment_text')->nullable();
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
        Schema::drop('timpzymr0120');
    }
}
