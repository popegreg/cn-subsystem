<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\PlSetting;

class CreateTblPackinglistSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('tbl_packinglist_setting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('assign');
            $table->string('user');
            $table->string('prodline');
            $table->timestamps();
        });

        /* checkedby TS */
        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Cherry Q.',
            'prodline' => 'TS'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Kaye G.',
            'prodline' => 'TS'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Jeng H.',
            'prodline' => 'TS'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Cris S.',
            'prodline' => 'TS'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Marissa M.',
            'prodline' => 'TS'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'SCR',
            'prodline' => 'TS'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Acel R.',
            'prodline' => 'TS'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Jojo P.',
            'prodline' => 'TS'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'RNS',
            'prodline' => 'TS'
        ]);

        /* checkedby YF */

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Loida C.',
            'prodline' => 'YF'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'RNS',
            'prodline' => 'YF'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Jojo P.',
            'prodline' => 'YF'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Tom N.',
            'prodline' => 'YF'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Rubie Z.',
            'prodline' => 'YF'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Lito M.',
            'prodline' => 'YF'
        ]);

        /* checkedby CN */

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Irma C.',
            'prodline' => 'CN'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Gian C.',
            'prodline' => 'CN'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Cathee M.',
            'prodline' => 'CN'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Loida C.',
            'prodline' => 'CN'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Rochelle De G.',
            'prodline' => 'CN'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Jojo P.',
            'prodline' => 'CN'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'RNS',
            'prodline' => 'CN'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Tom N.',
            'prodline' => 'CN'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Rubie Z.',
            'prodline' => 'CN'
        ]);

        PlSetting::create([
            'assign' => 'checkedby',
            'user' => 'Lito M.',
            'prodline' => 'CN'
        ]);



        /* preparedby TS */

        PlSetting::create([
            'assign' => 'preparedby',
            'user' => 'Romeo A.',
            'prodline' => 'TS'
        ]);

        /* preparedby CN */

        PlSetting::create([
            'assign' => 'preparedby',
            'user' => 'Irma C.',
            'prodline' => 'CN'
        ]);

        PlSetting::create([
            'assign' => 'preparedby',
            'user' => 'Gian C.',
            'prodline' => 'CN'
        ]);

        PlSetting::create([
            'assign' => 'preparedby',
            'user' => 'Cathee M.',
            'prodline' => 'CN'
        ]);

        PlSetting::create([
            'assign' => 'preparedby',
            'user' => 'Loida C.',
            'prodline' => 'CN'
        ]);

        PlSetting::create([
            'assign' => 'preparedby',
            'user' => 'Rochelle De G.',
            'prodline' => 'CN'
        ]);

        PlSetting::create([
            'assign' => 'preparedby',
            'user' => 'Rose M.',
            'prodline' => 'CN'
        ]);

        PlSetting::create([
            'assign' => 'preparedby',
            'user' => 'Dianne Z.',
            'prodline' => 'CN'
        ]);

        /* preparedby YF */

        PlSetting::create([
            'assign' => 'preparedby',
            'user' => 'Loida C.',
            'prodline' => 'YF'
        ]);

        PlSetting::create([
            'assign' => 'preparedby',
            'user' => 'Dianne Z.',
            'prodline' => 'YF'
        ]);

        PlSetting::create([
            'assign' => 'preparedby',
            'user' => 'Rose C.',
            'prodline' => 'YF'
        ]);

        PlSetting::create([
            'assign' => 'preparedby',
            'user' => 'Marie De G.',
            'prodline' => 'YF'
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
