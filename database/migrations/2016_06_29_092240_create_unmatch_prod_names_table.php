<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnmatchProdNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unmatch_prodname', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->string('r3_name');
            $table->integer('error',false, true)->length(1);
            $table->string('drawing_num');
            $table->string('r3_dn');
            $table->integer('error_dn',false, true)->length(1);
            $table->string('bu',5);
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
        Schema::drop('unmatch_prodname');
    }
}
