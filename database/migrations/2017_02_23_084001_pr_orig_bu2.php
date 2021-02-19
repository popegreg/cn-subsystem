<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrOrigBu2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pr_orig_bu2', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pr');
            $table->datetime('issuedate');
            $table->string('code');
            $table->string('partname');
            $table->integer('ordqty',false,true)->length()->nullable();
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
        Schema::drop('pr_orig_bu2');
    }
}
