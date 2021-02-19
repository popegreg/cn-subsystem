<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblWbsKitIssuance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_kit_issuance', function (Blueprint $table) {
            $table->increments('id');
            $table->string('issue_no');
            $table->string('po');
            $table->string('detailid');
            $table->string('item');
            $table->string('item_desc');
            $table->double('kit_qty',10,4)->nullable();
            $table->double('issued_qty',10,4)->nullable();
            $table->string('lot_no')->nullable();
            $table->string('location')->nullable();
            $table->string('remarks')->nullable();
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
