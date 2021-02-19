<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMjustificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mjustifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',20);
            $table->string('name',200);
            $table->string('create_pg', 50)->nullable();
            $table->string('create_user', 20)->nullable();
            $table->string('update_pg', 50)->nullable();
            $table->string('update_user', 20)->nullable();
            $table->index('code');
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
        Schema::drop('mjustifications');
    }
}
