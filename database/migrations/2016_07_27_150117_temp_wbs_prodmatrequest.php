<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TempWbsProdmatrequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_wbs_prodmatrequest', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po');
            $table->string('detailid');
            $table->string('code');
            $table->string('name');
            $table->string('lot_no');
            $table->string('classification');
            $table->double('issuedqty',9,2);
            $table->text('requestqty');
            $table->string('location');
            $table->string('remarks');
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
