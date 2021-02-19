<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Transaction;

class TblTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_transaction', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('description');
            $table->string('prefix');
            $table->string('prefixformat');
            $table->integer('nextno');
            $table->integer('nextnolength');
            $table->timestamps();

        });

        Transaction::create([
            'code' => 'MAT_RCV',
            'description' => 'Material Receiving',
            'prefix' => 'MAT-YYMM-',
            'prefixformat' => 'MAT-%y%m-',
            'nextno' => '249',
            'nextnolength' => '5',
        ]);
        Transaction::create([
            'code' => 'MKL_ISS',
            'description' => 'Material Kitting List and Issuance',
            'prefix' => 'WHS-YYMM-',
            'prefixformat' => 'WHS-%y%m-',
            'nextno' => '17',
            'nextnolength' => '5',
        ]);
        Transaction::create([
            'code' => 'PAR_PRD',
            'description' => 'Parts Production',
            'prefix' => 'PAP',
            'prefixformat' => '',
            'nextno' => '1',
            'nextnolength' => '7',
        ]);
        Transaction::create([
            'code' => 'PAR_RCV',
            'description' => 'Parts Recieving',
            'prefix' => 'PAR',
            'prefixformat' => '',
            'nextno' => '1',
            'nextnolength' => '7',
        ]);
        Transaction::create([
            'code' => 'PHY_INV',
            'description' => 'Physical Inventory',
            'prefix' => 'PHY-YYMM-',
            'prefixformat' => 'PHY-%y%m-',
            'nextno' => '1',
            'nextnolength' => '5',
        ]);
        Transaction::create([
            'code' => 'PRD_REQ',
            'description' => 'Production Material Request',
            'prefix' => 'PMR-YYMM-',
            'prefixformat' => 'PMR-%y%m-',
            'nextno' => '1',
            'nextnolength' => '5',
        ]);
        Transaction::create([
            'code' => 'PRD_SHP',
            'description' => 'Pre-Shipment',
            'prefix' => 'PRE',
            'prefixformat' => '',
            'nextno' => '1',
            'nextnolength' => '7',
        ]);
        Transaction::create([
            'code' => 'SAK_ISS',
            'description' => 'Sakidashi Issuance',
            'prefix' => 'SI-YYMM-',
            'prefixformat' => 'SI-%y%m-',
            'nextno' => '110',
            'nextnolength' => '5',
        ]);
        Transaction::create([
            'code' => 'WAR_ISS',
            'description' => 'Warehouse Material Issuance',
            'prefix' => 'WMI-YYMM-',
            'prefixformat' => 'WMI-%y%m-',
            'nextno' => '1',
            'nextnolength' => '5',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('tbl_transaction');
    }
}
