<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnmatchUSGTSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unmatch_usgts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('PO');
            $table->string('productcode');
            $table->string('productname');
            $table->string('partcode');
            $table->string('partname');
            $table->string('supplier');
            $table->string('kcode');
            $table->integer('error',false, true)->length(1);
            $table->integer('lv',false, true)->length(1);
            $table->integer('usg',false, true)->length(10)->nullable();
            $table->string('siyou');
            $table->integer('error_usg',false, true)->length(1);
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
        Schema::drop('unmatch_usgts');
    }
}
