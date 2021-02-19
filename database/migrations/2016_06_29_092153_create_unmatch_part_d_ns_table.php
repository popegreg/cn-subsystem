<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnmatchPartDNsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unmatch_partdn', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('partname');
            $table->string('drawing_num');
            $table->string('r3_dn');
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
        Schema::drop('unmatch_partdn');
    }
}
