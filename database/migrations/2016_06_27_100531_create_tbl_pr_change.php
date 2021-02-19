<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPrChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pr_change', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sales_no')->nullable();
            $table->string('sales_type')->nullable();
            $table->string('sales_org')->nullable();
            $table->string('commercial')->nullable();
            $table->string('section')->nullable();
            $table->string('sales_branch')->nullable();
            $table->string('sales_g')->nullable();
            $table->string('supplier')->nullable();
            $table->string('destination')->nullable();
            $table->string('player')->nullable();
            $table->string('assistant')->nullable();
            $table->string('po_num')->nullable();
            $table->date('issued_date')->nullable();
            $table->date('flight_need_date')->nullable();
            $table->string('headertext')->nullable();
            $table->string('pcode')->nullable();
            $table->string('itemtext')->nullable();
            $table->integer('orderqty')->nullable();
            $table->string('unit')->nullable();
            $table->string('classification')->nullable();
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
