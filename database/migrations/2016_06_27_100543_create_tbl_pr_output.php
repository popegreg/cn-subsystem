<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPrOutput extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pr_output', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po_num',20)->nullable();
            $table->date('date_issued')->nullable();
            $table->string('pcode',20)->nullable();
            $table->string('partname',150)->nullable();
            $table->double('unit_price',10,4)->nullable();
            $table->double('orig_qty',10,0)->nullable();
            $table->double('new_qty',10,0)->nullable();
            $table->double('amount',10,4)->nullable();
            $table->string('bikou',50)->nullable();
            $table->timestamps();
            $table->index('id');
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
