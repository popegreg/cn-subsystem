<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnmatchSUPTSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unmatch_supts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partcode');
            $table->string('partname');
            $table->string('r3_sup');
            $table->string('vendor');
            $table->integer('error',false, true)->length(1);
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
        Schema::drop('unmatch_supts');
    }
}
