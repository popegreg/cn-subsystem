<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Prrs;

class CreatePrrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prrs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('period_covered',20);
            $table->string('standard1',20);
            $table->string('lower_limit_price',20);
            $table->string('standard2',20);
            $table->string('for_gr_po',20);
            $table->timestamps();
        });

        Prrs::create([
            'period_covered' => '3',
            'standard1' => '0.50',
            'lower_limit_price' => '100.00',
            'standard2' => '0.00',
            'for_gr_po' => '5'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('prrs');
    }
}