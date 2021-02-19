<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblMrpPpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_mrp_pps', function (Blueprint $table) 
        {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->string('order_no');
            $table->mediumInteger('sched_qty');
            $table->dateTime('ppdr_reply');
            $table->string('re');
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
        Schema::drop('tbl_mrp_pps');
    }
}
