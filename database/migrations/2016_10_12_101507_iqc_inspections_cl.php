<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IqcInspectionsCl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iqc_inspections_cl', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_no_cl');
            $table->string('partcode_cl');
            $table->string('partname_cl');
            $table->string('supplier_cl');   
            $table->string('app_date_cl');
            $table->string('app_time_cl');
            $table->string('app_no_cl');
            $table->string('lot_no_cl');
            $table->integer('lot_qty_cl',false, true)->length(20);
            $table->string('date_ispected_cl');
            $table->string('ww_cl');
            $table->string('fy_cl');
            $table->string('shift_cl');
            $table->string('time_ins_from_cl');
            $table->string('time_ins_to_cl');
            $table->string('inspector_cl');
            $table->string('submission_cl');
            $table->string('judgement_cl');
            $table->string('lot_inspected_cl');
            $table->string('lot_accepted_cl');
            $table->integer('no_of_defects_cl',false, true)->length(20);
            $table->string('remarks_cl');
            $table->string('dbcon_cl');       
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
        //
    }
}
