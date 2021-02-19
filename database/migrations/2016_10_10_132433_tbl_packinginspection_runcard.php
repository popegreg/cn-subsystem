<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblPackinginspectionRuncard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_packinginspection_runcard', function(Blueprint $table){
            $table->increments('id');
            $table->string('pono');
            $table->string('carton_no');
            $table->string('runcard_no');
            $table->string('runcard_qty');
            $table->string('runcard_remarks');
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
