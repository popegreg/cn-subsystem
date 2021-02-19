<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblWbsKit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_material_kitting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('issuance_no');
            $table->string('po_no');
            $table->string('device_code');
            $table->string('device_name');
            $table->double('po_qty',10,4)->nullable();
            $table->double('kit_qty',10,4)->nullable();
            $table->string('kit_no')->nullable();
            $table->string('prepared_by')->nullable();
            $table->string('status')->nullable();
            $table->date('issuance_date');
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
