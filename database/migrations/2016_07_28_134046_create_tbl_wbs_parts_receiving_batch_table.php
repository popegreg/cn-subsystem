<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblWbsPartsReceivingBatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_parts_receiving_batch', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('wbs_pr_id',20);
            $table->string('item',20);
            $table->float('qty',20,4)->default('0');
            $table->float('box_qty',20,4)->default('0');
            $table->string('lot_no', 20)->nullable();
            $table->string('location', 200)->nullable();
            $table->string('is_printed', 2)->default('0');
            $table->string('create_user', 20)->nullable();
            $table->string('update_user', 20)->nullable();
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
        Schema::drop('tbl_wbs_parts_receiving_batch');
    }
}
