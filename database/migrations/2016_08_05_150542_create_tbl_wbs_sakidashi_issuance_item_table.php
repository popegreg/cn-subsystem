<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblWbsSakidashiIssuanceItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_sakidashi_issuance_item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('issuance_no',20);
            $table->string('item', 20)->nullable();
            $table->string('item_desc', 200)->nullable();
            $table->string('lot_no', 20)->nullable();
            $table->string('pair_no', 20)->nullable();
            $table->float('issued_qty',20,4)->default('0');
            $table->float('required_qty',20,4)->default('0');
            $table->float('return_qty',20,4)->default('0');
            $table->date('sched_return_date')->nullable();
            $table->string('remarks',200)->nullable();
            $table->date('issuance_date');
            $table->string('create_user', 20)->nullable();
            $table->string('update_user', 20)->nullable();
            $table->timestamps();
            $table->index('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_wbs_sakidashi_issuance_item');
    }
}
