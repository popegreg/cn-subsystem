<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\mProductline;

class CreateMproductlinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mproductlines', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',20);
            $table->string('name',200);
            $table->string('create_pg', 50)->nullable();
            $table->string('create_user', 20)->nullable();
            $table->string('update_pg', 50)->nullable();
            $table->string('update_user', 20)->nullable();
            $table->enum('delete_flag', ['0','1'])->default('0');
            $table->timestamps();
        });

        mProductline::create([
            'code' => 'CN',
            'name' => 'BU1(CN)',
            'create_pg' => '2003',
            'create_user' => 'admin'
        ]);

        mProductline::create([
            'code' => 'TS',
            'name' => 'BU2(TS)',
            'create_pg' => '2003',
            'create_user' => 'admin'
        ]);

        mProductline::create([
            'code' => 'YF',
            'name' => 'CONNECTORS(YF)',
            'create_pg' => '2003',
            'create_user' => 'admin'
        ]);
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
