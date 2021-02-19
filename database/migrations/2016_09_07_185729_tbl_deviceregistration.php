<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblDeviceregistration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_deviceregistration', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pono');
            $table->string('devicename');
            $table->string('family');
            $table->string('series');
            $table->string('ptype');
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
