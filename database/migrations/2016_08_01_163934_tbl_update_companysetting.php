<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Company;

class TblUpdateCompanysetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('tbl_update_companysetting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('address');
            $table->string('tel1');
            $table->string('tel2');
            $table->timestamps();
        });

        Company::create([
            'name' => 'Pricon Microelectronics, Inc.',
            'address' => '#14 Ampere St., Light Industry and Science Park 1, Cabuyao, Laguna',
            'tel1' =>'(+632) 843 local 116',
            'tel2' =>'',
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
