<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWbsMaterialReceivingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_material_receiving', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('receive_no',20)->unique();
            $table->date('receive_date')->nullable();
            $table->string('invoice_no', 20)->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('pallet_no', 20)->nullable();
            $table->float('total_qty',20,4)->default('0');
            $table->float('total_var',20,4)->default('0');
            $table->float('total_amt',20,4)->default('0');
            $table->string('status',20)->nullable();
            $table->string('app_date',20)->nullable();
            $table->string('app_time',20)->nullable();
            $table->dateTime('create_pg', 50)->nullable();
            $table->string('create_user', 20)->nullable();
            $table->dateTime('update_pg', 50)->nullable();
            $table->string('update_user', 20)->nullable();
            $table->timestamps();
            $table->index('receive_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_wbs_material_receiving');
    }
}
