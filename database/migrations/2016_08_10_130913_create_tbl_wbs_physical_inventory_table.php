<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblWbsPhysicalInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_physical_inventory', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('inventory_no',20)->unique();
            $table->string('location', 50)->nullable();
            $table->dateTime('inventory_date')->nullable();
            $table->dateTime('actual_date')->nullable();
            $table->string('counted_by', 50)->nullable();
            $table->string('remarks', 200)->nullable();
            $table->string('status', 20)->nullable();
            $table->string('create_user', 20)->nullable();
            $table->string('update_user', 20)->nullable();
            $table->timestamps();
            $table->index('inventory_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_wbs_physical_inventory');
    }
}
