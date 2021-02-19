<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnmatchBomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unmatch_bom', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po')->nullable();
            $table->string('prodcode')->nullable();
            $table->string('prodname')->nullable();
            $table->string('partcode')->nullable();
            $table->string('partname')->nullable();
            $table->string('supplier')->nullable();
            $table->string('ycode')->nullable();
            $table->integer('error',false, true)->length(1)->default(0);
            $table->string('lv')->nullable();
            $table->string('usage')->nullable();
            $table->string('r3usage')->nullable();
            $table->integer('errorflg',false, true)->length(1)->default(0);
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
        Schema::drop('unmatch_bom');
    }
}
