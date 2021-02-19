<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblWbsMaterialDisposition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wbs_material_disposition',function(Blueprint $table){
            $table->increments('id');
            $table->string('itemcode');
            $table->string('itemname');
            $table->string('lotno');
            $table->string('lotqty');
            $table->string('disposition');
            $table->string('createdby');
            $table->string('createddate');
            $table->string('updatedby');
            $table->string('updateddate');
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
