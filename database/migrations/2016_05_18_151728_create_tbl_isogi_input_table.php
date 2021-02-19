<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblIsogiInputTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_isogi_input', function (Blueprint $table) {
            $table->increments('id');
            $table->date('r3_arrival_date');
            $table->string('yec_po',200);
            $table->string('code',200);
            $table->string('name',200);
            $table->string('dw',200);
            $table->string('whs',200);
            $table->mediumInteger('po_qty');
            $table->mediumInteger('arrival_qty');
            $table->string('sup_code',200);
            $table->string('supplier',200);
            $table->date('pickup_date');
            $table->string('inspection_class',200);
            $table->string('line',200);
            $table->string('remarks',200);
            $table->string('po',200);
            $table->string('production_name',200);
            $table->string('pr',200);
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
        Schema::drop('tbl_isogi_input');
    }
}