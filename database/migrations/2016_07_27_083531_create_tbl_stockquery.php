<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblStockquery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_stockquery', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->string('vendor')->nullable;
            $table->double('price',10,4);
            $table->double('whssm',20,4);
            $table->double('whsnon',20,4);
            $table->double('whs102',20,4);
            $table->double('whs100',20,4);
            $table->double('assy100',20,4);
            $table->double('assy102',20,4);
            $table->double('stocktotal',20,4);
            $table->double('requirement',20,4);
            $table->double('available',20,4);
            $table->double('prbalance',20,4);
            $table->string('prodcode');
            $table->string('prodname');
            $table->double('usage',20,4);
            $table->string('updated');
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
