<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WbsInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_inventory', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('wbs_mr_id',20);
            $table->string('invoice_no');
            $table->string('item',20);
            $table->string('item_desc',200)->nullable();
            $table->float('qty',20,4)->default('0');
            $table->string('box',20)->nullable();
            $table->float('box_qty',20,4)->default('0');
            $table->string('lot_no', 20)->nullable();
            $table->string('location', 200)->nullable();
            $table->string('supplier', 100)->nullable();
            $table->string('drawing_num', 200)->nullable();
            $table->string('iqc_status', 2)->default('0');
            $table->string('is_printed', 2)->default('0');
            $table->integer('for_kitting',false, true)->length(2);
            $table->integer('not_for_iqc',false, true)->length(2);
            $table->string('iqc_result')->nullable();
            $table->date('received_date', 50)->nullable();
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
        Schema::drop('tbl_wbs_inventory');
    }
}
