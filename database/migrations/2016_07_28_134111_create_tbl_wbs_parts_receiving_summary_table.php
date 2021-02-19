<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblWbsPartsReceivingSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_parts_receiving_summary', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('wbs_pr_id',20);
            $table->string('item',20);
            $table->string('item_desc', 200);
            $table->float('qty',20,4)->default('0');
            $table->float('received_qty',20,4)->default('0');
            $table->float('variance',20,4)->default('0');
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
        Schema::drop('tbl_wbs_parts_receiving_summary');
    }
}
