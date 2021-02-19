<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InvoiceDataCheckXtank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_data_check_xtank', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->nullable();
            $table->string('vendor')->nullable();
            $table->double('price',20,4)->nullable();
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
        Schema::drop('invoice_data_check_xtank');
    }
}
