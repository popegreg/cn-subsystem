<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrChangeWorkBu2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pr_change_work_bu2', function (Blueprint $table) {
            $table->increments('id');
            $table->string('orderno')->nullable();
            $table->datetime('issueddate')->nullable();
            $table->string('code')->nullable();
            $table->integer('newqty',false, true)->length(20)->nullable();
            $table->string('bikou')->nullable();
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
        Schema::drop('pr_change_work_bu2');
    }
}
