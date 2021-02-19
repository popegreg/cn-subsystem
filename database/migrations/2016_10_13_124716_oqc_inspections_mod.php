<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OqcInspectionsMod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oqc_inspections_mod',function(Blueprint $table){
            $table->increments('id');
            $table->string('pono');
            $table->string('device');
            $table->string('lotno');
            $table->string('submission');
            $table->string('mod1');
            $table->string('qty');
            $table->string('modid');
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
