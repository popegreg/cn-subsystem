<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblRequestDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_request_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transno');
            $table->string('whstransno');
            $table->string('detailid');
            $table->string('code');
            $table->string('name');
            $table->string('classification')->nullable();
            $table->double('issuedqty',20,4);
            $table->double('requestqty',20,4);
            $table->double('servedqty',20,4);
            $table->string('location')->nullable();
            $table->string('remarks')->nullable();
            $table->string('lot_no')->nullable();
            $table->string('requestedby')->nullable();
            $table->string('acknowledgeby')->nullable();
            $table->string('last_served_by')->nullable();
            $table->string('last_served_date')->nullable();
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
