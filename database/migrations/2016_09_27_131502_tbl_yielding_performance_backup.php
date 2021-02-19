<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblYieldingPerformanceBackup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_yielding_performance_backup', function (Blueprint $table) {
            $table->increments('id');
            $table->string('yieldingno');
            $table->string('pono');
            $table->string('poqty');
            $table->string('device');
            $table->string('family');
            $table->string('series');
            $table->string('prodtype');
            $table->string('classification');
            $table->string('mod');
            $table->string('qty');
            $table->string('productiondate');
            $table->string('yieldingstation');
            $table->string('accumulatedoutput');
            $table->string('toutput');
            $table->string('treject');
            $table->string('tmng');
            $table->string('tpng');
            $table->string('ywomng');
            $table->string('twoyield');
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
