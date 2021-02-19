<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrMoqExessSummaryBu2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pr_moq_exess_summary_bu2', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category')->nullable();
            $table->string('orderno')->nullable();
            $table->datetime('issuedate')->nullable();
            $table->string('ym')->nullable();
            $table->string('code')->nullable();
            $table->string('partname')->nullable();
            $table->double('unitprice')->nullable();
            $table->integer('originalqty',false,true)->length(20)->nullable();
            $table->integer('newqty',false,true)->length(20)->nullable();
            $table->integer('moqexcess',false,true)->length(20)->nullable();
            $table->double('amount',20,4)->nullable();
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
        Schema::drop('pr_moq_exess_summary_bu2');
    }
}
