<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPackingListDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_packing_list_details', function (Blueprint $table) 
        {
            $table->bigIncrements('id');
            $table->string('packing_id',20);
            $table->string('po',20);
            $table->string('description', 200);
            $table->string('item_code', 20);
            $table->float('price',20,4);
            $table->string('box_no',50);
            $table->integer('qty',false, true)->length(20);
            $table->string('gross_weight',20);
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
        Schema::drop('tbl_packing_list_details');
    }
}
