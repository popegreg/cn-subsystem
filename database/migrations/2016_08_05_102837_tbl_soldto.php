<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SoldTo;

class TblSoldto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('tbl_soldto', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('code');
            $table->string('companyname');
            $table->string('description');
            $table->timestamps();
        });

       SoldTo::create([
            'code' => '10001',
            'companyname' => 'YAMAICHI ELECTRONICS U.S.A. INC.',
            'description' => 'YAMAICHI ELECTRONICS U.S.A. INC.
475 HOLGER WAY, SAN JOSE
CA 95134 U.S.A.
TEL: 1-408-715-9100
ATTN:  MS. PAT BECKER',
        ]);

        SoldTo::create([
             'code' => '10002',
             'companyname' => '1ST INC',
             'description' => '1ST INC
NO.19,PUDING RD
HSINCHU CITY,
TAIWAN
TEL:886-3-578-2266
ATTN:MS. FENNA WANG',
         ]);

         SoldTo::create([
              'code' => '10003',
              'companyname' => 'AMSG-SJ',
              'description' => 'AMSG-SJ
3850 N.FIRST STREET SANJOSE,
CA 95134
TEL:408-643-6318
ATTN:MR.THAI NINH',
          ]);

          SoldTo::create([
               'code' => '10004',
               'companyname' => 'ESA ELECTRONICS PTE LTD.',
               'description' => 'ESA ELECTRONICS PTE LTD.
BLK 16 KALLANG PLACE#05-10/18
KALLANG BASIN INDUSTRIAL ESTATE
SINGAPORE 339156
TEL:65-6296-1613
ATTN: MR.VELLAN A/L PACHIAPPEN',
           ]);

           SoldTo::create([
                'code' => '10005',
                'companyname' => 'SANDISK SEMICONDUCTOR (SHANGHAI) CO.,LTD',
                'description' => 'SANDISK SEMICONDUCTOR (SHANGHAI) CO.,LTD
NO.388 JIANGCHUAN EAST RD,
MINHANG DISTRICT SHANGHAI 200241, CHINA
TEL. 8621-6090-5953
MR. KELIANG CAO',
            ]);

            SoldTo::create([
                 'code' => '10006',
                 'companyname' => 'TCL YAMAICHI TAIWAN INC.',
                 'description' => 'TCL YAMAICHI TAIWAN INC.
4F-1, NO. 192, DONG-GUANG ROAD, EAST DISTRICT,
HSIN CHU CITY TAIWAN
TEL: 886-3-515-2299
ATTN. MR. YASUYUKI ONO',
             ]);

             SoldTo::create([
                  'code' => '10007',
                  'companyname' => 'TEST SOLUTION SERVICES, INC.',
                  'description' => 'TEST SOLUTION SERVICES, INC.
LOT 8, BLK, 5, CNB STREET LAGUNA INTERNATIONAL
INDUSTRIAL PARK MAMPLASAN, BINAN, LAGUNA,
PHILIPPINES 4024
TEL: 6349-539-1222
ATTN: MR. POLLY DEL MUNDO',
              ]);

              SoldTo::create([
                   'code' => '10008',
                   'companyname' => 'YAMAICHI ELECTRONICS SINGAPORE PTE LTD.',
                   'description' => 'YAMAICHI ELECTRONICS SINGAPORE PTE LTD.
72 BENDEMEER ROAD #04-21, LUZERNE
SINGAPORE 339941
TEL: 65-6297-8312
ATTN: MR. JUSTIN NG',
               ]);

               SoldTo::create([
                    'code' => '10009',
                    'companyname' => 'ABREL PRODUCTS',
                    'description' => 'ABREL PRODUCTS
RAHEER, BUSINESS PARK, LIMERICK IRELAND
TEL:35361304566
ATTN: MR. OWEN PRILLAMAN',
                ]);

                SoldTo::create([
                     'code' => '10010',
                     'companyname' => 'ADVANTEST CORPORATION GUNMA FACTORY',
                     'description' => 'ADVANTEST CORPORATION GUNMA FACTORY
54-1,SHINOZUKA,ORA-MACHI
ORA-GUN,GUNMA 370-0615
JAPAN
TEL:81-276-88-7500
ATTN:MR.MAKOTO NOMA',
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
