<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIQCInspectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iqc_inspections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_no');
            $table->string('partcode');
            $table->string('partname');
            $table->string('supplier');   
            $table->string('app_date');
            $table->string('app_time');
            $table->string('app_no');
            $table->string('lot_no');
            $table->integer('lot_qty',false, true)->length(20);
            $table->string('type_of_inspection');
            $table->string('severity_of_inspection');
            $table->string('inspection_lvl');
            $table->string('aql');
            $table->integer('accept',false, true)->length(20);
            $table->integer('reject',false, true)->length(20);
            $table->string('date_ispected');
            $table->string('ww');
            $table->string('fy');
            $table->string('shift');
            $table->string('time_ins_from');
            $table->string('time_ins_to');
            $table->string('inspector');
            $table->string('submission');
            $table->string('judgement');
            $table->string('lot_inspected');
            $table->string('lot_accepted');
            $table->string('sample_size');
            $table->integer('no_of_defects',false, true)->length(20);
            $table->string('remarks');
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
        Schema::drop('iqc_inspections');
    }
}
