<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOQCInspectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oqc_inspections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('assembly_line');
            $table->string('lot_no');
            $table->string('app_date');
            $table->string('app_time');
            $table->string('prod_category');
            $table->string('po_no');
            $table->string('device_name');
            $table->string('customer');
            $table->string('po_qty');
            $table->string('family');
            $table->string('type_of_inspection');
            $table->string('severity_of_inspection');
            $table->string('inspection_lvl');
            $table->string('aql');
            $table->integer('accept',false, true)->length(20);
            $table->integer('reject',false, true)->length(20);
            $table->string('date_inspected');
            $table->string('ww');
            $table->string('fy');
            $table->string('shift');
            $table->string('time_ins_from');
            $table->string('time_ins_to');
            $table->string('inspector');
            $table->string('submission');
            $table->string('coc_req');
            $table->string('judgement');
            $table->integer('lot_qty',false, true)->length(20);
            $table->string('sample_size');
            $table->string('lot_inspected');
            $table->string('lot_accepted');
            $table->string('lot_rejected');
            $table->integer('num_of_defects',false, true)->length(20);
            $table->string('remarks');
            $table->string('dbcon');
            $table->string('modid');
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
        Schema::drop('oqc_inspections');
    }
}
