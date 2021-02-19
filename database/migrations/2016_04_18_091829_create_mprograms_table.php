<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\mProgram;

class CreateMprogramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mprograms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('program_code',20);
            $table->string('program_name',100);
            $table->string('program_class',50);
            $table->text('program_description')->nullable();
            $table->string('create_pg',50)->nullable();
            $table->string('create_user',20)->nullable();
            $table->string('update_pg',50)->nullable();
            $table->string('update_user',20)->nullable();
            $table->enum('delete_flag', ['0','1'])->default('0');
            $table->timestamps();
            $table->index('program_code');
        });

        mProgram::create([
            'program_code' => '2001',
            'program_name' => 'User Master',
            'program_class' => 'Master Management',
            'program_description' => 'Master Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '2002',
            'program_name' => 'Supplier Master',
            'program_class' => 'Master Management',
            'program_description' => 'Master Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '2003',
            'program_name' => 'Product Line Master',
            'program_class' => 'Master Management',
            'program_description' => 'Master Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '2004',
            'program_name' => 'Reason Master',
            'program_class' => 'Master Management',
            'program_description' => 'Master Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '2005',
            'program_name' => 'Dropdowns Maintenance',
            'program_class' => 'Master Management',
            'program_description' => 'Master Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '2006',
            'program_name' => 'Sold To Master',
            'program_class' => 'Master Management',
            'program_description' => 'Master Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3001',
            'program_name' => 'Order Data Check',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3002',
            'program_name' => 'YPICS R3 Order Data Report',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3003',
            'program_name' => 'MRA',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3004',
            'program_name' => 'PRRS',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3005',
            'program_name' => 'Invoice Data Check',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3006',
            'program_name' => 'Material List for Direct',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3007',
            'program_name' => 'MRP Calculation',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => 'SSS',
            'program_name' => 'Scheduling Support Subsystem',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3008',
            'program_name' => 'P.O. Status',
            'program_class' => 'SSS',
            'program_description' => 'SSS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3009',
            'program_name' => 'Parts Status',
            'program_class' => 'SSS',
            'program_description' => 'SSS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3010',
            'program_name' => 'Delivery Warning',
            'program_class' => 'SSS',
            'program_description' => 'SSS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3011',
            'program_name' => 'Data Update',
            'program_class' => 'SSS',
            'program_description' => 'SSS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3012',
            'program_name' => 'Answer Input Management',
            'program_class' => 'SSS',
            'program_description' => 'SSS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3013',
            'program_name' => 'Sample Douji Input',
            'program_class' => 'SSS',
            'program_description' => 'SSS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3014',
            'program_name' => 'PR Change',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3015',
            'program_name' => 'PR Balance Difference Check',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3016',
            'program_name' => 'YPICS Stocks Query',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => 'WBS',
            'program_name' => 'WBS',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3017',
            'program_name' => 'Material Receiving',
            'program_class' => 'WBS',
            'program_description' => 'WBS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3018',
            'program_name' => 'IQC Inspection',
            'program_class' => 'WBS',
            'program_description' => 'WBS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3019',
            'program_name' => 'Material Kitting & Issuance',
            'program_class' => 'WBS',
            'program_description' => 'WBS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3020',
            'program_name' => 'Sakidashi Issuance',
            'program_class' => 'WBS',
            'program_description' => 'WBS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3021',
            'program_name' => 'Parts Receiving',
            'program_class' => 'WBS',
            'program_description' => 'WBS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3022',
            'program_name' => 'Physical Inventory',
            'program_class' => 'WBS',
            'program_description' => 'WBS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3023',
            'program_name' => 'Production Material Request',
            'program_class' => 'WBS',
            'program_description' => 'WBS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3024',
            'program_name' => 'Production Material Return',
            'program_class' => 'WBS',
            'program_description' => 'WBS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3025',
            'program_name' => 'Warehouse Material Issuance',
            'program_class' => 'WBS',
            'program_description' => 'WBS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3026',
            'program_name' => 'Material Disposition',
            'program_class' => 'WBS',
            'program_description' => 'WBS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3027',
            'program_name' => 'WBS Reports',
            'program_class' => 'WBS',
            'program_description' => 'WBS',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3028',
            'program_name' => 'Packing List System',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => 'QCDB',
            'program_name' => 'QC Database',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3029',
            'program_name' => 'IQC Inspection',
            'program_class' => 'QCDB',
            'program_description' => 'QCDB',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3030',
            'program_name' => 'OQC Inspection',
            'program_class' => 'QCDB',
            'program_description' => 'QCDB',
            'create_user' => 'admin'
        ]);
        mProgram::create([
            'program_code' => '3031',
            'program_name' => 'FGS',
            'program_class' => 'QCDB',
            'program_description' => 'QCDB',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3032',
            'program_name' => 'Packing Inspection',
            'program_class' => 'QCDB',
            'program_description' => 'QCDB',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => 'QCMLD',
            'program_name' => 'QC Database Molding',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3033',
            'program_name' => 'OQC Inspection',
            'program_class' => 'QCMLD',
            'program_description' => 'QCMLD',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3034',
            'program_name' => 'Packing Inspection',
            'program_class' => 'QCMLD',
            'program_description' => 'QCMLD',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3035',
            'program_name' => 'Yield Performance',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '3036',
            'program_name' => 'YPICS Invoicing',
            'program_class' => 'Operational Management',
            'program_description' => 'Operational Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '4001',
            'program_name' => 'Account Management',
            'program_class' => 'Security Management',
            'program_description' => 'Security Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '4002',
            'program_name' => 'WBS Settings',
            'program_class' => 'Security Management',
            'program_description' => 'Security Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '4003',
            'program_name' => 'Transactions Settings',
            'program_class' => 'Security Management',
            'program_description' => 'Security Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '4004',
            'program_name' => 'Company Settings',
            'program_class' => 'Security Management',
            'program_description' => 'Security Management',
            'create_user' => 'admin'
        ]);

        mProgram::create([
            'program_code' => '4005',
            'program_name' => 'Packing List Settings',
            'program_class' => 'Security Management',
            'program_description' => 'Security Management',
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
