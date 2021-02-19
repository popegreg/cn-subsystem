<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\WbsSetting;
class TblWbsSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('tbl_wbssetting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->string('value');
            $table->timestamps();
        });

       WbsSetting::create([
            'name' => 'auto_logout',
            'description' => 'Logout due to inactivity after (minutes)',
            'value' => '1000',
        ]);

       WbsSetting::create([
            'name' => 'rpt_viewer_cmd',
            'description' => 'URL command for Report Viewer',
            'value' => 'http://192.168.3.235:8080/Birt/frameset?_reort=',
        ]);
       WbsSetting::create([
            'name' => 'Mobile Barcode Printer 1',
            'description' => 'Mobile Barcode Printer 1',
            'value' => '\\L-whs-07-0411\bt1',
        ]);
        WbsSetting::create([
            'name' => 'Mobile Barcode Printer 2',
            'description' => 'Mobile Barcode Printer 2',
            'value' => '\\T-whs-07-0414\bt2',
        ]);
        WbsSetting::create([
            'name' => 'Mobile Barcode Printer 3',
            'description' => 'Mobile Barcode Printer 3',
            'value' => '\\L-whs-08-0714\bt3',
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
