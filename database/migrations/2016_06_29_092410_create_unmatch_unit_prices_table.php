<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnmatchUnitPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unmatch_unitprice', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tid');
            $table->string('lv');
            $table->string('code');
            $table->string('partname');
            $table->string('vendor');
            $table->string('price');
            $table->string('r3_price');
            $table->integer('error',false, true)->length(1);
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
        Schema::drop('unmatch_unitprice');
    }
}
