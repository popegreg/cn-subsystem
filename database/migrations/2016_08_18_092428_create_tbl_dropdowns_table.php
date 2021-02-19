<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\mDropdowns;

class CreateTblDropdownsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_mdropdowns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->string('category');
            $table->timestamps();
        });
        // product destination
        mDropdowns::create(['category' => '1', 'description' => 'IC SOCKET',]);
        mDropdowns::create(['category' => '1', 'description' => 'Probe Pins',]);
        mDropdowns::create(['category' => '1', 'description' => 'Connectors',]);
        mDropdowns::create(['category' => '1', 'description' => 'FOL',]);
        mDropdowns::create(['category' => '1', 'description' => 'PPS',]);
        mDropdowns::create(['category' => '1', 'description' => 'PPS',]);
        mDropdowns::create(['category' => '1', 'description' => 'Rework',]);
        mDropdowns::create(['category' => '1', 'description' => 'Sorting',]);
        mDropdowns::create(['category' => '1', 'description' => 'IQC',]);

        // line destination
        for ($i=1; $i < 21; $i++) {
            mDropdowns::create(['category' => '2','description' => 'Line '.$i,]);
        }

        // Classification
        mDropdowns::create(['category' => '3','description' => 'Material NG (MNG)']);
        mDropdowns::create(['category' => '3','description' => 'Discrepancy']);
        mDropdowns::create(['category' => '3','description' => 'In-line Shortage']);
        mDropdowns::create(['category' => '3','description' => 'Production NG (PNG)']);
        mDropdowns::create(['category' => '3','description' => 'Partial Issuance']);
        mDropdowns::create(['category' => '3','description' => 'Wrong Parts']);
        mDropdowns::create(['category' => '3','description' => 'Evaluation']);
        mDropdowns::create(['category' => '3','description' => 'Others']);

        // Carrier
        mDropdowns::create(['category' => '4', 'description' => 'AGILITY']);
        mDropdowns::create(['category' => '4', 'description' => 'AGILITY (JAL 742)']);
        mDropdowns::create(['category' => '4', 'description' => 'AGILITY (HAND CARRY)']);
        mDropdowns::create(['category' => '4', 'description' => 'AGILITY (JAL FLIGHT)']);
        mDropdowns::create(['category' => '4', 'description' => 'AGILITY (J-SPEED)']);
        mDropdowns::create(['category' => '4', 'description' => 'AGILITY (PR426)']);
        mDropdowns::create(['category' => '4', 'description' => 'AGILITY (DELTA)']);
        mDropdowns::create(['category' => '4', 'description' => 'AIIRLIFT ASIA, INC.']);
        mDropdowns::create(['category' => '4', 'description' => 'AIR21']);
        mDropdowns::create(['category' => '4', 'description' => 'ASIA DIRECT CONSOLIDATORS']);
        mDropdowns::create(['category' => '4', 'description' => 'CEVA LOGISTICS (PHILS)']);
        mDropdowns::create(['category' => '4', 'description' => 'CPI TRANSPORT']);
        mDropdowns::create(['category' => '4', 'description' => 'CPI TRANSPORT (PR-501)']);
        mDropdowns::create(['category' => '4', 'description' => 'CPI TRANSPORT(SQ919)']);
        mDropdowns::create(['category' => '4', 'description' => 'DANZAS']);
        mDropdowns::create(['category' => '4', 'description' => 'DELTA 172']);
        mDropdowns::create(['category' => '4', 'description' => 'DHL (PHILIPPINES)']);
        mDropdowns::create(['category' => '4', 'description' => 'DHL']);
        mDropdowns::create(['category' => '4', 'description' => 'DHL (GLOBAL)']);
        mDropdowns::create(['category' => '4', 'description' => 'DHL ECONOMY']);
        mDropdowns::create(['category' => '4', 'description' => 'DHL EXPRESS']);
        mDropdowns::create(['category' => '4', 'description' => 'DHL GLOBAL FORWARDING']);
        mDropdowns::create(['category' => '4', 'description' => 'DIMERCO EXPRESS']);
        mDropdowns::create(['category' => '4', 'description' => 'DIMERCO EXPRESS PHILS']);
        mDropdowns::create(['category' => '4', 'description' => 'DRZ (TRUCK)']);
        mDropdowns::create(['category' => '4', 'description' => 'DSV']);
        mDropdowns::create(['category' => '4', 'description' => 'E.G.L.TRUCK']);
        mDropdowns::create(['category' => '4', 'description' => 'EASTRANS MANILA']);
        mDropdowns::create(['category' => '4', 'description' => 'EGL']);
        mDropdowns::create(['category' => '4', 'description' => 'EXEL']);
        mDropdowns::create(['category' => '4', 'description' => 'EXPEDITORS (PHILS)']);
        mDropdowns::create(['category' => '4', 'description' => 'FEDEX ECONOMY']);
        mDropdowns::create(['category' => '4', 'description' => 'FEDEX']);
        mDropdowns::create(['category' => '4', 'description' => 'FEDEX "P1"']);
        mDropdowns::create(['category' => '4', 'description' => "FEDEX INT'L ECONOMY"]);
        mDropdowns::create(['category' => '4', 'description' => 'GLOBAL AIR FRT']);
        mDropdowns::create(['category' => '4', 'description' => 'HAND CARRY BY VANTEC']);
        mDropdowns::create(['category' => '4', 'description' => 'HVANTEC (PHILS)']);
        mDropdowns::create(['category' => '4', 'description' => 'JAL 742']);
        mDropdowns::create(['category' => '4', 'description' => 'K W E (MANILA)']);
        mDropdowns::create(['category' => '4', 'description' => 'M O L']);
        mDropdowns::create(['category' => '4', 'description' => 'MENLO (PHILS)']);
        mDropdowns::create(['category' => '4', 'description' => 'MORRISON EXPRESS']);
        mDropdowns::create(['category' => '4', 'description' => 'MULTI-AXIS HANDLERS']);
        mDropdowns::create(['category' => '4', 'description' => 'NEC LOGISTICS (PHILS)']);
        mDropdowns::create(['category' => '4', 'description' => 'NIPPON EXPRESS (PHILS)']);
        mDropdowns::create(['category' => '4', 'description' => 'NISSIN TRANSFORT']);
        mDropdowns::create(['category' => '4', 'description' => 'NNR (PHILS)']);

        // port of detination
        mDropdowns::create(['category' => '5', 'description' => 'BAGUIO CITY, PHILIPPINES']);
        mDropdowns::create(['category' => '5', 'description' => 'BINAN, LAGUNA, PHILIPPINES']);
        mDropdowns::create(['category' => '5', 'description' => 'CALAMBA, LAGUNA, PHILIPPINES']);
        mDropdowns::create(['category' => '5', 'description' => 'CAVITE, PHILIPPINES']);
        mDropdowns::create(['category' => '5', 'description' => 'CHINA']);
        mDropdowns::create(['category' => '5', 'description' => 'CONDA PORTUGAL']);
        mDropdowns::create(['category' => '5', 'description' => 'COSTA RICA']);
        mDropdowns::create(['category' => '5', 'description' => 'FRANCE']);
        mDropdowns::create(['category' => '5', 'description' => 'FUKUOKA, JAPAN']);
        mDropdowns::create(['category' => '5', 'description' => 'GEN. TRIAS, CAVITE']);
        mDropdowns::create(['category' => '5', 'description' => 'GERMANY']);
        mDropdowns::create(['category' => '5', 'description' => 'GREAT BRITAIN']);
        mDropdowns::create(['category' => '5', 'description' => 'HONG KONG']);
        mDropdowns::create(['category' => '5', 'description' => 'INDIA']);
        mDropdowns::create(['category' => '5', 'description' => 'IRELAND']);
        mDropdowns::create(['category' => '5', 'description' => 'ISRAEL']);
        mDropdowns::create(['category' => '5', 'description' => 'ITALY']);
        mDropdowns::create(['category' => '5', 'description' => 'JAPAN']);
        mDropdowns::create(['category' => '5', 'description' => 'LAGUNA, PHILIPPINES']);
        mDropdowns::create(['category' => '5', 'description' => 'MAKATI']);
        mDropdowns::create(['category' => '5', 'description' => 'MALAYSIA']);
        mDropdowns::create(['category' => '5', 'description' => 'MANILA, PHILIPPINES']);

        // description of goods
        mDropdowns::create(['category' => '6', 'description' => 'CONNECTORS']);
        mDropdowns::create(['category' => '6', 'description' => 'NCV']);
        mDropdowns::create(['category' => '6', 'description' => 'PACKAGING (TS MANUFACTURING)']);
        mDropdowns::create(['category' => '6', 'description' => 'PACKAGING MATERIALS']);
        mDropdowns::create(['category' => '6', 'description' => 'PARTS FOR IC SOCKETS']);
        mDropdowns::create(['category' => '6', 'description' => 'PARTS FOR IC SOCKETS(NCV)']);
        mDropdowns::create(['category' => '6', 'description' => 'PARTS FOR PROBE PIN']);
        mDropdowns::create(['category' => '6', 'description' => 'PARTS FOR PROBE PIN(NCV)']);
        mDropdowns::create(['category' => '6', 'description' => 'PROBE OUTOUT']);
        mDropdowns::create(['category' => '6', 'description' => 'PROBE PIN']);
        mDropdowns::create(['category' => '6', 'description' => 'PROBE PIN OUT OUT']);
        mDropdowns::create(['category' => '6', 'description' => 'PROBE PIN(NCV)']);
        mDropdowns::create(['category' => '6', 'description' => 'RAW MATERIALS']);
        mDropdowns::create(['category' => '6', 'description' => 'RAW MATERIALS (PROBE PIN)']);
        mDropdowns::create(['category' => '6', 'description' => 'SEMI-CONDUCTOR SOCKET']);
        mDropdowns::create(['category' => '6', 'description' => 'TOOLS & JIGS']);
        mDropdowns::create(['category' => '6', 'description' => 'YEU BUSINESS SOCKET']);
        mDropdowns::create(['category' => '6', 'description' => 'PARTS FOR FLEXIBLE PRINTED CIRCUIT']);
        mDropdowns::create(['category' => '6', 'description' => 'FLEXIBLE PRINTED CIRCUIT']);

        // family
        mDropdowns::create(['category' => '8', 'description' => 'BGA']);
        mDropdowns::create(['category' => '8', 'description' => 'BGA-FP']);
        mDropdowns::create(['category' => '8', 'description' => 'LGA']);
        mDropdowns::create(['category' => '8', 'description' => 'PGA']);
        mDropdowns::create(['category' => '8', 'description' => 'PGA-LGA']);
        mDropdowns::create(['category' => '8', 'description' => 'PUS']);
        mDropdowns::create(['category' => '8', 'description' => 'QFN']);
        mDropdowns::create(['category' => '8', 'description' => 'QFP1']);
        mDropdowns::create(['category' => '8', 'description' => 'QFP2']);
        mDropdowns::create(['category' => '8', 'description' => 'Socket No.2']);
        mDropdowns::create(['category' => '8', 'description' => 'SOJ']);
        mDropdowns::create(['category' => '8', 'description' => 'SON']);
        mDropdowns::create(['category' => '8', 'description' => 'TSOP']);

        // Series
        mDropdowns::create(['category' => '9', 'description' => 'Series101']);
        mDropdowns::create(['category' => '9', 'description' => 'Series102']);
        mDropdowns::create(['category' => '9', 'description' => 'Series103']);

        // mode of defects for yielding
        mDropdowns::create(['category' => '10', 'description' => 'Bent Crown']);
        mDropdowns::create(['category' => '10', 'description' => 'Bent Pin']);
        mDropdowns::create(['category' => '10', 'description' => 'Broken Parts (Plunger/Barrel)']);
        mDropdowns::create(['category' => '10', 'description' => 'Bubbles']);
        mDropdowns::create(['category' => '10', 'description' => 'Corrosion']);
        mDropdowns::create(['category' => '10', 'description' => 'Crack']);
        mDropdowns::create(['category' => '10', 'description' => 'Damage on Crimp Area']);
        mDropdowns::create(['category' => '10', 'description' => 'Damage on Top Point']);
        mDropdowns::create(['category' => '10', 'description' => 'Deformed Barrel']);
        mDropdowns::create(['category' => '10', 'description' => 'Deformed Plunger']);
        mDropdowns::create(['category' => '10', 'description' => 'Deformed Spring']);
        mDropdowns::create(['category' => '10', 'description' => 'Dent on Barrel']);
        mDropdowns::create(['category' => '10', 'description' => 'Dent on Petal']);
        mDropdowns::create(['category' => '10', 'description' => 'Dent on Plunger']);
        mDropdowns::create(['category' => '10', 'description' => 'Dent on Spring']);
        mDropdowns::create(['category' => '10', 'description' => 'Different Appearance']);
        mDropdowns::create(['category' => '10', 'description' => 'Discoloration / Bad Plating']);
        mDropdowns::create(['category' => '10', 'description' => 'Excess Metal']);
        mDropdowns::create(['category' => '10', 'description' => 'Excess Metal on Spring']);
        mDropdowns::create(['category' => '10', 'description' => 'Excess Parts']);
        mDropdowns::create(['category' => '10', 'description' => 'Failed on Movement Check']);
        mDropdowns::create(['category' => '10', 'description' => 'Failed on Moving Test']);
        mDropdowns::create(['category' => '10', 'description' => 'Failed on Resistance Check']);
        mDropdowns::create(['category' => '10', 'description' => 'Foreign Material']);
        mDropdowns::create(['category' => '10', 'description' => 'Gap']);
        mDropdowns::create(['category' => '10', 'description' => 'Gap / Loose Plunger']);
        mDropdowns::create(['category' => '10', 'description' => 'Loose Spring/Plunger']);
        mDropdowns::create(['category' => '10', 'description' => 'Maximum Diameter']);
        mDropdowns::create(['category' => '10', 'description' => 'Maximum Total Pin Length']);
        mDropdowns::create(['category' => '10', 'description' => 'Minimum Diameter']);
        mDropdowns::create(['category' => '10', 'description' => 'Minimum Total Pin Length']);
        mDropdowns::create(['category' => '10', 'description' => 'Missing Parts']);
        mDropdowns::create(['category' => '10', 'description' => 'NG on Total Pin Length']);
        mDropdowns::create(['category' => '10', 'description' => 'Peeled-off Plating']);
        mDropdowns::create(['category' => '10', 'description' => 'Plunger Distortion']);
        mDropdowns::create(['category' => '10', 'description' => 'Scratch on Barrel']);
        mDropdowns::create(['category' => '10', 'description' => 'Scratch on Plunger']);
        mDropdowns::create(['category' => '10', 'description' => 'Scratch on Spring']);
        mDropdowns::create(['category' => '10', 'description' => 'Spring Distortion']);
        mDropdowns::create(['category' => '10', 'description' => 'Stain']);
        mDropdowns::create(['category' => '10', 'description' => 'Stuck-up Plunger']);
        mDropdowns::create(['category' => '10', 'description' => 'Uneven Crimping']);
        mDropdowns::create(['category' => '10', 'description' => 'Wobble']);
        mDropdowns::create(['category' => '10', 'description' => 'Wrong Crimping']);
        mDropdowns::create(['category' => '10', 'description' => 'Wrong Orientation of Parts']);
        mDropdowns::create(['category' => '10', 'description' => 'Wrong Parts Used']);
        
        // Yielding Station
        mDropdowns::create(['category' => '11', 'description' => 'Machine']);
        mDropdowns::create(['category' => '11', 'description' => 'First Visual Inspection']);
        mDropdowns::create(['category' => '11', 'description' => 'Final Visual Inspection']);

        // Severity of Inspection
        mDropdowns::create(['category' => '12', 'description' => 'Normal']);
        mDropdowns::create(['category' => '12', 'description' => 'Tightened']);
        mDropdowns::create(['category' => '12', 'description' => 'Reduced']);

        //Inspection Level
        mDropdowns::create(['category' => '13', 'description' => 'S2']);
        mDropdowns::create(['category' => '13', 'description' => 'S3']);
        mDropdowns::create(['category' => '13', 'description' => 'II']);

        // die number
        for ($i=1; $i < 11; $i++) {
            mDropdowns::create(['category' => '15','description' => 'Die No. '.$i,]);
        }

        // Type of Inspection
        mDropdowns::create(['category' => '16', 'description' => 'Single']);
        mDropdowns::create(['category' => '16', 'description' => 'Double']);

        // Submission
        mDropdowns::create(['category' => '17', 'description' => '1st']);
        mDropdowns::create(['category' => '17', 'description' => '2nd']);
        mDropdowns::create(['category' => '17', 'description' => '3rd']);

        // AQL
        mDropdowns::create(['category' => '18', 'description' => '0.40']);
        mDropdowns::create(['category' => '18', 'description' => '0.65']);
        mDropdowns::create(['category' => '18', 'description' => '1.00']);
        mDropdowns::create(['category' => '18', 'description' => '0.25']);
        mDropdowns::create(['category' => '18', 'description' => '0.10']);
        mDropdowns::create(['category' => '18', 'description' => '0.15']);

        //Packing Type
        mDropdowns::create(['category' => '20', 'description' => 'Cylinder Type']);
        mDropdowns::create(['category' => '20', 'description' => 'Pallet Case Type']);
        mDropdowns::create(['category' => '20', 'description' => 'Tray Type']);

        //Unit Condition
        mDropdowns::create(['category' => '21', 'description' => 'Terminal Up']);
        mDropdowns::create(['category' => '21', 'description' => 'Terminal Down']);
        mDropdowns::create(['category' => '21', 'description' => 'Terminal Mounted on Esafoam']);

        //Packing Operator
        mDropdowns::create(['category' => '22', 'description' => 'Gemma']);
        mDropdowns::create(['category' => '22', 'description' => 'Melba']);
        mDropdowns::create(['category' => '22', 'description' => 'Mhy']);

        //Packing Code Per Series
        mDropdowns::create(['category' => '23', 'description' => '1']);
        mDropdowns::create(['category' => '23', 'description' => '2']);
        mDropdowns::create(['category' => '23', 'description' => '3']);

        //JUDGEMENT
        mDropdowns::create(['category' => '24', 'description' => 'JDGMT1']);
        mDropdowns::create(['category' => '24', 'description' => 'JDGMT2']);
        mDropdowns::create(['category' => '24', 'description' => 'JDGMT3']);

        //PRODUCT TYPE
        mDropdowns::create(['category' => '25', 'description' => 'PTYPE1']);
        mDropdowns::create(['category' => '25', 'description' => 'PTYPE2']);
        mDropdowns::create(['category' => '25', 'description' => 'PTYPE3']);

        // shift
        mDropdowns::create(['category' => '26', 'description' => 'Shift A']);
        mDropdowns::create(['category' => '26', 'description' => 'Shift B']);

        //mod oqc molding
        mDropdowns::create(['category' => '27', 'description' => 'Short Shot']);
        mDropdowns::create(['category' => '27', 'description' => 'Excess Plastic']);
        mDropdowns::create(['category' => '27', 'description' => 'Excess Metal']);
        mDropdowns::create(['category' => '27', 'description' => 'Broken']);
        mDropdowns::create(['category' => '27', 'description' => 'Stain']);
        mDropdowns::create(['category' => '27', 'description' => 'Dent']);
        mDropdowns::create(['category' => '27', 'description' => 'Toolmark']);
        mDropdowns::create(['category' => '27', 'description' => 'Missing']);
        mDropdowns::create(['category' => '27', 'description' => 'Humps']);
        mDropdowns::create(['category' => '27', 'description' => 'Clogged']);
        mDropdowns::create(['category' => '27', 'description' => 'Gas mark']);
        mDropdowns::create(['category' => '27', 'description' => 'Discoloration']);
        mDropdowns::create(['category' => '27', 'description' => 'Sinkmark']);
        mDropdowns::create(['category' => '27', 'description' => 'Warping']);
        mDropdowns::create(['category' => '27', 'description' => 'Foreign Material']);
        mDropdowns::create(['category' => '27', 'description' => 'Scratch']);
        mDropdowns::create(['category' => '27', 'description' => 'Crack']);
        mDropdowns::create(['category' => '27', 'description' => 'Step']);
        mDropdowns::create(['category' => '27', 'description' => 'Weld Line']);
        mDropdowns::create(['category' => '27', 'description' => 'Breakage']);
        mDropdowns::create(['category' => '27', 'description' => 'Mixed']);
        mDropdowns::create(['category' => '27', 'description' => 'Un-reworked']);
        mDropdowns::create(['category' => '27', 'description' => 'Wrong Product']);
        mDropdowns::create(['category' => '27', 'description' => 'Transformation']);
        mDropdowns::create(['category' => '27', 'description' => 'Void']);

        //Mode of Defect for IQC Inspection
        mDropdowns::create(['category' => '28', 'description' => 'MOD-IQC1']);
        mDropdowns::create(['category' => '28', 'description' => 'MOD-IQC2']);
        mDropdowns::create(['category' => '28', 'description' => 'MOD-IQC3']);

        //Mode of Defect for OQC Inspection
        mDropdowns::create(['category' => '30', 'description' => 'MOD-OQC1']);
        mDropdowns::create(['category' => '30', 'description' => 'MOD-OQC2']);
        mDropdowns::create(['category' => '30', 'description' => 'MOD-OQC3']);

        //Mode of Defect for Packing Inspection
        mDropdowns::create(['category' => '31', 'description' => 'NDF']);
        mDropdowns::create(['category' => '31', 'description' => 'Quantity Discrepancy']);
        mDropdowns::create(['category' => '31', 'description' => 'Wrong Declaration of Carton No.']);
        mDropdowns::create(['category' => '31', 'description' => 'Wrong Destination']);
        mDropdowns::create(['category' => '31', 'description' => 'Wrong Orientation of Unit']);
        mDropdowns::create(['category' => '31', 'description' => 'Wrong Packing Label']);
        mDropdowns::create(['category' => '31', 'description' => 'Wrong Packing Type']);
        mDropdowns::create(['category' => '31', 'description' => 'Wrong P.O Number']);
        mDropdowns::create(['category' => '31', 'description' => 'Wrong Series Name']);

        //OQC Inspection Assembly Line
        mDropdowns::create(['category' => '32', 'description' => 'Line 1 (BGA/LGA/YEU)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 1 (BGA-FP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 1 (Probe pin)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 1 (QFP/TSOP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 2 (BGA/LGA)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 2 (BGA-FP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 2 (Probe pin)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 2 (QFP/TSOP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 3 (BGA/LGA)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 3 (BGA-FP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 3 (Probe pin)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 3 (QFP/TSOP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 4 (BGA/LGA)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 4 (BGA-FP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 4 (Probe pin)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 4 (QFP/TSOP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 5 (BGA/LGA)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 5 (BGA-FP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 5 (Probe pin)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 5 (QFP/TSOP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 6 (BGA/LGA)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 6 (BGA-FP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 6 (Probe pin)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 6 (PS/SMPO)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 7 (BGA/LGA)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 7 (BGA-FP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 7 (Probe pin)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 7 (PS/SMPO)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 8 (BGA/LGA)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 8 (BGA-FP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 8 (Probe pin)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 9 (BGA/LGA)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 9 (BGA-FP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 9 (Probe pin)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 10 (BGA/LGA)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 10 (BGA-FP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 10 (Probe pin)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 11 (BGA-FP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 11 (Probe pin)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 12 (BGA-FP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 12 (Probe pin)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 13 (BGA-FP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 13 (Probe pin)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 14 (BGA-FP)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 14 (Probe pin)']);
        mDropdowns::create(['category' => '32', 'description' => 'Line 15 (Probe pin)']);

        //Mode of Defect for Packing Inspection
        






















       

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_mdropdowns');
    }
}
