<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblWbsIqcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_iqc', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('wbs_mr_id',2);
            $table->string('receive_no',20);
            $table->string('item',20);
            $table->string('status',20);
            $table->float('received_qty',20,4)->default('0')->nullable();
            $table->string('lot_no',20)->nullable();
            $table->string('drawing_no',50);
            $table->string('iqc_result',200);
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
        Schema::drop('tbl_wbs_iqc');
    }
}
