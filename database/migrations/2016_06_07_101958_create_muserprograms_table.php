<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\mUserprogram;

class CreateMuserprogramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('muserprograms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('program_code',20);
            $table->string('user_id',20);
            $table->integer('id_tblusers');
            $table->string('program_name',100);
            $table->enum('read_write', ['0', '1', '2'])->default('0');//no selection . readwrite . readonly
            $table->string('create_pg', 50)->default('0');
            $table->string('create_user', 20)->default('0');
            $table->string('update_pg', 50)->nullable();
            $table->string('update_user', 20)->nullable();
            $table->enum('delete_flag', ['0','1'])->default('0');
            $table->index(['program_code','user_id']);
            $table->timestamps();
        });

        mUserprogram::create([
            'program_code' => '2001',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'User Master',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '2002',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Supplier Master',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '2003',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Product Line Master',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '2004',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Reason Master',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '2005',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Dropdowns Maintenance',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '2006',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Sold Master',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3001',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Order Data Check',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3002',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'YPICS R3 Order Data Report',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3003',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'MRA',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3004',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'PPRS',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3005',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Invoice Data Check',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3006',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Material List for Direct',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3007',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'MRP Calculation',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => 'SSS',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Scheduling Support Subsystem',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3008',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'P.O. Status',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3009',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Parts Status',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3010',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Delivery Warning',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3011',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Data Update',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3012',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Answer Input Management',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3013',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Sample Douji Input',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3014',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'PR Change',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3015',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'PR Balance Difference Check',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3016',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'YPICS Stocks Query',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => 'WBS',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'WBS',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3017',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Material Receiving',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3018',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'IQC Inspection',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3019',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Material Kitting & Issuance',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3020',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Sakidashi Issuance',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3021',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Parts Receiving',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3022',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Physical Inventory',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3023',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Production Material Request',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3024',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Production Material Return',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3025',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Warehouse Material Issuance',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3026',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Material Disposition',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3027',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'WBS Reports',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3028',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Packing List System',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => 'QCDB',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'QC Database',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3029',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'IQC Inspection',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3030',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'OQC Inspection',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3031',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'FGS',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3032',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Packing Inspection',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => 'QCMLD',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'QC Database Molding',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3033',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'OQC Inspection',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3034',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Packing Inspection',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3035',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Yield Performance',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '3036',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'YPICS Invoicing',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);


        mUserprogram::create([
            'program_code' => '4001',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Account Management',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '4002',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'WBS Settings',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '4003',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Transactions Settings',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '4004',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Company Setting',
            'read_write' => 1,
            'create_pg' => '2001',
            'create_user' => 'admin'
        ]);

        mUserprogram::create([
            'program_code' => '4005',
            'user_id' => 'admin',
            'id_tblusers' => 1,
            'program_name' => 'Packing List Setting',
            'read_write' => 1,
            'create_pg' => '2001',
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
