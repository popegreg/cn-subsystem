<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWbsMaterialReceivingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_material_receiving_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('wbs_mr_id',20);
            $table->string('item',20);
            $table->string('item_desc', 200);
            $table->float('qty',20,4)->default('0');
            $table->string('pr_no',20,4)->nullable();
            $table->float('unit_price')->default('0');
            $table->float('amount',20,4)->default('0');
            $table->dateTime('create_pg', 50)->nullable();
            $table->string('create_user', 20)->nullable();
            $table->dateTime('update_pg', 50)->nullable();
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
        Schema::drop('tbl_wbs_material_receiving_details');
    }
}
