<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Momscheck extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('momscheck', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po');
            $table->string('code');
            $table->string('prodname');
            $table->string('kcode');
            $table->string('lvl');
            $table->string('vendor');
            $table->string('usage');
            $table->string('qty');
            $table->string('siyou');
            $table->string('ypics_qty');
            $table->string('diff1');
            $table->string('moms');
            $table->string('withdrawal_qty');
            $table->string('diff2');
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
        Schema::drop('momscheck');
    }
}
