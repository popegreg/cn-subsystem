<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYpicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ypics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('salesno',200)->nullable();
            $table->string('salestype',200)->nullable();
            $table->string('salesorg',200)->nullable();
            $table->string('commercial',200)->nullable();
            $table->string('section',200)->nullable();
            $table->string('salesbrand',200)->nullable();
            $table->string('salesg',200)->nullable();
            $table->string('supplier',200)->nullable();
            $table->string('destination',200)->nullable();
            $table->string('payer',200)->nullable();
            $table->string('assistant',200)->nullable();
            $table->string('purchaseorderno',200)->nullable();
            $table->string('issuedate',200)->nullable();
            $table->string('flightneeddate',200)->nullable();
            $table->string('headertext',200)->nullable();
            $table->string('code',200)->nullable();
            $table->string('itemtext',200)->nullable();
            $table->string('orderquantity',200)->nullable();
            $table->string('unit',200)->nullable();
            $table->string('token',200)->nullable();
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
