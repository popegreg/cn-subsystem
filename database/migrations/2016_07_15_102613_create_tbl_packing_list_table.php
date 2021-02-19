<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPackingListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_packing_list', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('control_no',20)->unique();
            $table->string('invoice_date');
            $table->string('invoice_no', 20);
            $table->string('remarks_time');
            $table->string('remarks_pickupdate');
            $table->string('remarks_s_no', 200);
            $table->string('sold_to_id',20);
            $table->string('sold_to',200);
            $table->string('ship_to',200);
            $table->string('carrier',100);
            $table->string('date_ship');
            $table->string('port_loading',100);
            $table->string('port_destination',100);
            $table->string('description_of_goods',100);
            $table->string('shipping_instruction',200)->nullable();
            $table->string('case_marks',200);
            $table->string('note',200);
            $table->string('from',50);
            $table->string('to',50);
            $table->string('freight',50);
            $table->string('preparedby',80);
            $table->string('checkedby');
            $table->string('grossweight_invoicing');
            $table->string('invoicing_status');
            $table->string('create_user', 20)->nullable();
            $table->string('update_user', 20)->nullable();
            $table->timestamps();
            $table->index('control_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_packing_list');
    }
}
