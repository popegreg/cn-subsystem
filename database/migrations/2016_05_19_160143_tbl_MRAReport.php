<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblMRAReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_MRAReport', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ItemCode',25);
            $table->string('ItemName',50)->nullable();
            $table->string('ItemType',20)->nullable();
            $table->integer('TtlRequired')->nullable();
            $table->integer('TtlCompleted')->nullable();
            $table->integer('ReqToComplete')->nullable();
            $table->integer('WHSE100')->nullable();
            $table->integer('WHSE102')->nullable();
            $table->integer('WHSE_NON')->nullable();
            $table->integer('ASSY100')->nullable();
            $table->integer('ASSY102')->nullable();
            $table->integer('WHSE106')->nullable();
            $table->integer('WHSESM')->nullable();
            $table->integer('TotalOnHand')->nullable();
            $table->integer('OrderBalance')->nullable();
            $table->integer('ForOrdering')->nullable();
            $table->string('MAINBUMO',20)->nullable();
            $table->string('Remarks',20)->nullable();
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
        Schema::drop('tbl_MRAReport');
    }
}
