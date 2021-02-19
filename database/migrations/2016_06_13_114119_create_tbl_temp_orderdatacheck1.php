<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTempOrderdatacheck1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_orderdatacheck1', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po');
            $table->string('partsname');
            $table->integer('div_usage',false, true)->default(0);
            $table->string('kcode');
            $table->double('usage',20,4)->default('0');
            $table->string('qty')->nullable();
            $table->string('vendor')->nullable();
            $table->string('drawing_num')->nullable();
            $table->string('jdate')->nullable();
            $table->string('unit')->nullable();
            $table->index('po');
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
