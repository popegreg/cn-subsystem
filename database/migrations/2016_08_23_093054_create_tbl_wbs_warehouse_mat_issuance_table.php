<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblWbsWarehouseMatIssuanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_warehouse_mat_issuance', function (Blueprint $table) {
            $table->increments('id');
            $table->string('issuance_no');
            $table->string('request_no');
            $table->string('status')->nullable();
            $table->double('total_req_qty',20,4)->default('0');
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
        Schema::drop('tbl_wbs_warehouse_mat_issuance');
    }
}
