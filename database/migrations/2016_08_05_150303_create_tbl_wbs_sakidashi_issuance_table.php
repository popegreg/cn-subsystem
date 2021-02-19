<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblWbsSakidashiIssuanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_sakidashi_issuance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('issuance_no',20)->unique();
            $table->string('po_no', 20)->nullable();
            $table->string('device_code', 20)->nullable();
            $table->string('device_name', 200)->nullable();
            $table->double('po_qty',20,4)->default('0');
            $table->string('incharge',20)->nullable();
            $table->string('remarks',200)->nullable();
            $table->string('status',20)->nullable();
            $table->string('create_user', 20)->nullable();
            $table->string('update_user', 20)->nullable();
            $table->timestamps();
            $table->index('issuance_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_wbs_sakidashi_issuance');
    }
}
