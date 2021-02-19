<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOQCInspectionMoldingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oqc_inspection_moldings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po_no');
            $table->string('partcode');
            $table->string('partname');
            $table->string('customer');
            $table->string('family');
            $table->double('total_qty',20,4);
            $table->string('die_no');
            $table->double('qty',20,4);
            $table->double('lot_qty',20,4)->nullable();
            $table->string('lot_no');
            $table->string('type_of_inspection');
            $table->string('severity_of_inspection');
            $table->string('inspection_lvl');
            $table->string('aql');
            $table->integer('accept',false, true)->length(20);
            $table->integer('reject',false, true)->length(20);
            $table->string('date_inspected');
            $table->string('shift');
            $table->string('inspector');
            $table->string('submission');
            $table->string('visual_operator');
            $table->string('fy_no');
            $table->string('ww_no');
            $table->string('remarks');
            $table->string('ptcp_tnr');
            $table->string('lot_inspected');
            $table->string('lot_accepted');
            $table->string('lot_rejected');
            $table->string('sample_size');
            $table->integer('num_of_defectives',false, true)->length(20);
            $table->string('judgement');
            $table->string('from');
            $table->string('to');
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
        Schema::drop('oqc_inspection_moldings');
    }
}
