<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblWbsPhysicalInventoryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_physical_inventory_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('wbs_pi_id',20);
            $table->string('item',20)->nullable();
            $table->string('item_desc',100)->nullable();
            $table->string('location', 200)->nullable();
            $table->float('whs100',20,4)->default('0');
            $table->float('whs102',20,4)->default('0');
            $table->float('whsnon',20,4)->default('0');
            $table->float('whssm',20,4)->default('0');
            $table->float('whsng',20,4)->default('0');
            $table->float('actual_qty',20,4)->default('0');
            $table->float('variance',20,4)->default('0');
            $table->string('remarks', 200)->nullable();
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
        Schema::drop('tbl_wbs_physical_inventory_details');
    }
}
