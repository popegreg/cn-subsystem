<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\mSeriesRegistration;
class TblSeriesregistration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_seriesregistration', function (Blueprint $table) {
            $table->increments('id');
            $table->string('family');
            $table->string('series');
            $table->timestamps();
        });

        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'BIBGA',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC280',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC398',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC409',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC449',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC551',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC511',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC530',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC537',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC538',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC542',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC547',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC561',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC564',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC567',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC-797',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'IC-873',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP178',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP236',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP276',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP352',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP437',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP444',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP481',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP483',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP486',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP504',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP510',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP524',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP537',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP541',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP556',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP559',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP563',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP566',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP571',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP587',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP590',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'NP595',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'TCBGA',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA',
            'series' => 'TCLGA',
        ]);

        //BGA-FP-----------------------------------
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP291',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP351',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP352',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP367',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP378',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP383',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP413',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP436',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP442',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP446',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP448',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP467',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP482',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP494',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP513',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP515',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP562',
        ]);
        mSeriesRegistration::create([
            'family' => 'BGA-FP',
            'series' => 'NP568',
        ]);

        //LGA----------------------------
        mSeriesRegistration::create([
            'family' => 'LGA',
            'series' => 'ACC',
        ]);
        mSeriesRegistration::create([
            'family' => 'LGA',
            'series' => 'IC273',
        ]);
        mSeriesRegistration::create([
            'family' => 'LGA',
            'series' => 'IC280',
        ]);
        mSeriesRegistration::create([
            'family' => 'LGA',
            'series' => 'IC405',
        ]);
        mSeriesRegistration::create([
            'family' => 'LGA',
            'series' => 'IC569',
        ]);
        mSeriesRegistration::create([
            'family' => 'LGA',
            'series' => 'IC807',
        ]);
        mSeriesRegistration::create([
            'family' => 'LGA',
            'series' => 'IC836',
        ]);
        mSeriesRegistration::create([
            'family' => 'LGA',
            'series' => 'IC871',
        ]);
        mSeriesRegistration::create([
            'family' => 'LGA',
            'series' => 'LGA11',
        ]);
        mSeriesRegistration::create([
            'family' => 'LGA',
            'series' => 'NP364',
        ]);
        mSeriesRegistration::create([
            'family' => 'LGA',
            'series' => 'NP404',
        ]);
        mSeriesRegistration::create([
            'family' => 'LGA',
            'series' => 'NP469',
        ]);
        mSeriesRegistration::create([
            'family' => 'LGA',
            'series' => 'NP488',
        ]);
        mSeriesRegistration::create([
            'family' => 'LGA',
            'series' => 'NP514',
        ]);

        //PGA-----------------------------
        mSeriesRegistration::create([
            'family' => 'PGA',
            'series' => 'NP236',
        ]);
        mSeriesRegistration::create([
            'family' => 'PGA',
            'series' => 'NP382',
        ]);
        mSeriesRegistration::create([
            'family' => 'PGA',
            'series' => 'NP565',
        ]);
        mSeriesRegistration::create([
            'family' => 'PGA',
            'series' => 'NP89',
        ]);

        //PGA-LGA-------------------------
        mSeriesRegistration::create([
            'family' => 'PGA-LGA',
            'series' => 'NP438',
        ]);

        //PUS-----------------------------
        mSeriesRegistration::create([
            'family' => 'PUS',
            'series' => 'IC149',
        ]);

        //PROBE PIN---------------------------
        mSeriesRegistration::create([
            'family' => 'Probe Pin',
            'series' => '4-Point Dent',
        ]);
        mSeriesRegistration::create([
            'family' => 'Probe Pin',
            'series' => 'Press/Roll',
        ]);
        mSeriesRegistration::create([
            'family' => 'Probe Pin',
            'series' => 'Spring-Lock',
        ]);

        //QFN----------------------------------
        mSeriesRegistration::create([
            'family' => 'QFN',
            'series' => 'IC549',
        ]);
        mSeriesRegistration::create([
            'family' => 'QFN',
            'series' => 'IC550',
        ]);
        mSeriesRegistration::create([
            'family' => 'QFN',
            'series' => 'IC552',
        ]);
        mSeriesRegistration::create([
            'family' => 'QFN',
            'series' => 'IC553',
        ]);
        mSeriesRegistration::create([
            'family' => 'QFN',
            'series' => 'IC564',
        ]);
        mSeriesRegistration::create([
            'family' => 'QFN',
            'series' => 'IC-837',
        ]);
        mSeriesRegistration::create([
            'family' => 'QFN',
            'series' => 'IC-857',
        ]);
        mSeriesRegistration::create([
            'family' => 'QFN',
            'series' => 'NP445',
        ]);
        mSeriesRegistration::create([
            'family' => 'QFN',
            'series' => 'NP473',
        ]);
        mSeriesRegistration::create([
            'family' => 'QFN',
            'series' => 'NP506',
        ]);
        mSeriesRegistration::create([
            'family' => 'QFN',
            'series' => 'NP560',
        ]);
        mSeriesRegistration::create([
            'family' => 'QFN',
            'series' => 'NP583',
        ]);
        mSeriesRegistration::create([
            'family' => 'QFN',
            'series' => 'NP584',
        ]);

        //SOJ-------------------------------
        mSeriesRegistration::create([
            'family' => 'SOJ',
            'series' => 'IC100',
        ]);
        mSeriesRegistration::create([
            'family' => 'SOJ',
            'series' => 'IC107',
        ]);

        //TSOP---------------------------------
        mSeriesRegistration::create([
            'family' => 'TSOP',
            'series' => 'IC162',
        ]);
        mSeriesRegistration::create([
            'family' => 'TSOP',
            'series' => 'IC189',
        ]);
        mSeriesRegistration::create([
            'family' => 'TSOP',
            'series' => 'IC191',
        ]);
        mSeriesRegistration::create([
            'family' => 'TSOP',
            'series' => 'IC235',
        ]);
        mSeriesRegistration::create([
            'family' => 'TSOP',
            'series' => 'IC237',
        ]);
        mSeriesRegistration::create([
            'family' => 'TSOP',
            'series' => 'IC296',
        ]);
        mSeriesRegistration::create([
            'family' => 'TSOP',
            'series' => 'IC297',
        ]);
        mSeriesRegistration::create([
            'family' => 'TSOP',
            'series' => 'IC354',
        ]);
        mSeriesRegistration::create([
            'family' => 'TSOP',
            'series' => 'IC363',
        ]);
        mSeriesRegistration::create([
            'family' => 'TSOP',
            'series' => 'IC369',
        ]);
        mSeriesRegistration::create([
            'family' => 'TSOP',
            'series' => 'IC385',
        ]);
        mSeriesRegistration::create([
            'family' => 'TSOP',
            'series' => 'IC389',
        ]);
        mSeriesRegistration::create([
            'family' => 'TSOP',
            'series' => 'IC403',
        ]);
        mSeriesRegistration::create([
            'family' => 'TSOP',
            'series' => 'IC503',
        ]);

        //Socket No.2
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'ACC',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'AD234',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'AD51',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'ADACC',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-102',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-114',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-115',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-118',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-120',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC121',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-132',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC135',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC166',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-176',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-179',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC200',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC203',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC211',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-214',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-222',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-24',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-258',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-276',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-287',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC299',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-333',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-334',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-336',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-349',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC37',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-377',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC37F',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-39',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-393',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-401',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC438',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-445',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-46',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-464',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC485',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-487',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-49',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-497',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC499',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-500',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-53',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC558',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC576',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-581',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-589',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC59',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC595',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC601',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC634',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-639',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-657',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-66',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC674',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-699',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC70',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-742',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-746',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-747',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-754',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-755',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-760',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-762',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-776',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-807',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-830',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-832',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-834',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-841',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC848',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-91',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'IC-99',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'ICC34',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'LC030',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'LC050',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'LC-31',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'LCC',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'PS42',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'PS44',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'PS49',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'PS61',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'SMT',
        ]);
        mSeriesRegistration::create([
            'family' => 'Socket No.2',
            'series' => 'TAB',
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
