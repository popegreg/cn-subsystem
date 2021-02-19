<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblWbsWarehouseMatIssuanceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_warehouse_mat_issuance_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('issuance_no');
            $table->string('request_no');
            $table->string('pmr_detail_id');
            $table->string('detail_id');
            $table->string('item');
            $table->string('item_desc');
            $table->float('request_qty',20,4)->default('0');
            $table->float('issued_qty_o',20,4)->default('0');
            $table->float('issued_qty_t',20,4)->default('0');
            $table->string('lot_no')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->nullable();
            $table->string('return_status')->nullable();
            $table->string('return_qty')->nullable();
            $table->string('receivedby')->nullable();
            $table->date('returndate')->nullable();
            $table->string('returnedby')->nullable();
            $table->text('return_remarks')->nullable();
            $table->string('create_user')->nullable();
            $table->string('update_user')->nullable();
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
        Schema::drop('tbl_wbs_warehouse_mat_issuance_details');
    }
}
