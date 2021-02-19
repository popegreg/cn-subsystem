<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderDataReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_data_report', function (Blueprint $table) {
            $table->increments('id');
            $table->string('salesno')->nullable();
            $table->string('salestype')->nullable();
            $table->string('salesorg')->nullable();
            $table->string('commercial')->nullable();
            $table->string('section')->nullable();
            $table->string('salesbrand')->nullable();
            $table->string('salesg')->nullable();
            $table->string('supplier')->nullable();
            $table->string('destination')->nullable();
            $table->string('payer')->nullable();
            $table->string('assistant')->nullable();
            $table->string('purchaseorderno')->nullable();
            $table->string('issuedate')->nullable();
            $table->string('flightneeddate')->nullable();
            $table->string('headertext')->nullable();
            $table->string('code')->nullable();
            $table->string('itemtext')->nullable();
            $table->integer('orderquantity',false, true)->length(20);
            $table->string('unit')->nullable();
            $table->string('dbcon')->nullable();
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
        Schema::drop('order_data_report');
    }
}
