<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequestSummary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_request_summary', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transno');
            $table->string('whstransno');
            $table->string('pono');
            $table->string('destination')->nullable();
            $table->string('line')->nullable();
            $table->string('status');
            $table->string('requestedby')->nullable();
            $table->string('lastservedby')->nullable();
            $table->string('lastserveddate')->nullable();
            $table->string('createdby');
            $table->string('updatedby');
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
