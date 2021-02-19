<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\mDropdownCategory;

class CreateTblDropdownsCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_mdropdown_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category');
            $table->timestamps();
        });

        mDropdownCategory::create(['category' => 'Product Destination']); //1
        mDropdownCategory::create(['category' => 'Line Destination']); //2
        mDropdownCategory::create(['category' => 'Classification']);//3
        mDropdownCategory::create(['category' => 'Carrier']);//4
        mDropdownCategory::create(['category' => 'Port of Destination']);//5
        mDropdownCategory::create(['category' => 'Description of Goods']);//6
        mDropdownCategory::create(['category' => 'Yielding Performance']);//7
        mDropdownCategory::create(['category' => 'Family']);//8
        mDropdownCategory::create(['category' => 'Series']);//9
        mDropdownCategory::create(['category' => 'Mode of Defect - Yield Performance']);//10
        mDropdownCategory::create(['category' => 'Yielding Station']);//11
        mDropdownCategory::create(['category' => 'Severity of Inspection']);//12
        mDropdownCategory::create(['category' => 'Inspection Level']);//13
        mDropdownCategory::create(['category' => 'Inspector for OQC Database Molding']);//14
        mDropdownCategory::create(['category' => 'Die No']);//15
        mDropdownCategory::create(['category' => 'Type of Inspection']);//16
        mDropdownCategory::create(['category' => 'Submission']);//17
        mDropdownCategory::create(['category' => 'AQL']);//18
        mDropdownCategory::create(['category' => 'Customer']);//19
        mDropdownCategory::create(['category' => 'Packing Type for OQC Inspection']);//20
        mDropdownCategory::create(['category' => 'Unit Condition for OQC Inspection']);//21
        mDropdownCategory::create(['category' => 'Packing Operator for OQC Inspection']);//22
        mDropdownCategory::create(['category' => 'Packing Code(Per Series) for OQC Inspection']);//23
        mDropdownCategory::create(['category' => 'Judgement']);//24
        mDropdownCategory::create(['category' => 'Product Type']);//25
        mDropdownCategory::create(['category' => 'Shift']);//26
        mDropdownCategory::create(['category' => 'Mode of Defect - OQC Inscpection Molding']);//27
        mDropdownCategory::create(['category' => 'Mode of Defect - IQC Inspection']);//28
        mDropdownCategory::create(['category' => 'Inspector for IQC Database Inspection']);//29
        mDropdownCategory::create(['category' => 'Mode of Defect - OQC Inspection']);//30
        mDropdownCategory::create(['category' => 'Mode of Defect - Packing Inspection']);//31
        mDropdownCategory::create(['category' => 'Assembly Line']);//32
        mDropdownCategory::create(['category' => 'Packing Type for OQC Molding']);//33
        mDropdownCategory::create(['category' => 'Unit Condition for OQC Molding']);//34
        mDropdownCategory::create(['category' => 'Packing Operator for OQC Molding']);//35
        mDropdownCategory::create(['category' => 'Packing Code(Per Series) for OQC Molding']);//36
        mDropdownCategory::create(['category' => 'Mode of Defect - Packing Molding']);//37

  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_mdropdown_category');
    }
}