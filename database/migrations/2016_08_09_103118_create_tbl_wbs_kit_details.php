<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblWbsKitDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_material_kitting_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('issue_no');
            $table->string('po');
            $table->string('detailid');
            $table->string('item');
            $table->string('item_desc');
            $table->double('usage',20,4)->nullable();
            $table->double('rqd_qty',20,4)->nullable();
            $table->double('kit_qty',20,4)->nullable();
            $table->double('issued_qty',20,4)->nullable();
            $table->string('location')->nullable();
            $table->string('drawing_no')->nullable();
            $table->string('supplier')->nullable();
            $table->string('whs100');
            $table->string('whs102');
            $table->string('create_user');
            $table->string('update_user');
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
        //
    }
}
