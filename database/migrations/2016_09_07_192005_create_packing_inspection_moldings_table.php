<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackingInspectionMoldingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_inspection_moldings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partcode');
            $table->string('partname');
            $table->string('po_no');
            $table->integer('po_qty',false, true)->length(20);
            $table->string('shipment_date');
            $table->string('customer');
            $table->integer('qty',false, true)->length(20);
            $table->string('packing_code');
            $table->string('lot_no');
            $table->string('visual_operator');
            $table->string('date_inspected');
            $table->string('time_inspected');
            $table->string('packing_type');
            $table->string('remarks');
            $table->string('packing_operator');
            $table->string('judgement');
            $table->string('no_of_defects');
            $table->string('mode_of_defect');
            $table->string('dbcon');
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
        Schema::drop('packing_inspection_moldings');
    }
}
