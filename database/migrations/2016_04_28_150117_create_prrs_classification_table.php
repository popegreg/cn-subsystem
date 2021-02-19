<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrrsClassificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prrs_classification', function (Blueprint $table) {
            $table->increments('id');
            $table->string('prrs_id',20);
            $table->string('classification',200);
            $table->string('percentage',20);
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
        Schema::drop('prrs_classification');
    }
}
