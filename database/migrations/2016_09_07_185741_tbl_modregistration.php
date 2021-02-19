<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\mModRegistration;
class TblModregistration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_modregistration', function (Blueprint $table) {
            $table->increments('id');
            $table->string('family');
            $table->string('mod');
            $table->timestamps();
        });
        
        //TEST SOCKETS---------------------------- 
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Bent Crown'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Bent Pin'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Broken Parts (Plunger/Barrel)'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Bubbles'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Corrosion'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Crack'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Damage on Crimp Area'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Damage on Top Point'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Deformed Barrel'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Deformed Plunger'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Deformed Spring'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Dent on Barrel'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Dent on Petal'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Dent on Plunger'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Dent on Spring'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Different Appearance'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Discoloration / Bad Plating'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Excess Metal'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Excess Metal on Spring'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Excess Parts'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Failed on Movement Check'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Failed on Moving Test'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Failed on Resistance Check'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Foreign Material'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Gap'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Gap / Loose Plunger'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Loose Spring/Plunger'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Maximum Diameter'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Maximum Total Pin Length'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Minimum Diameter'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Minimum Total Pin Length'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Missing Parts'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'NG on Total Pin Length'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Peeled-off Plating'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Plunger Distortion'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Scratch on Barrel'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Scratch on Plunger'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Scratch on Spring'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Spring Distortion'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Stain'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Stuck-up Plunger'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Uneven Crimping'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Wobble'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Wrong Crimping'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Wrong Orientation of Parts'
        ]);
        mModRegistration::create([
            'family' => 'Test Socket',
            'mod' => 'Wrong Parts Used'
        ]);

        //BURN IN--------------------------------------------------------------------------------------------------------------------------------------------------
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Bad Plating on Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Bad Plating on Terminal'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Bent Terminal'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Breakage on Insulator'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Broken #SP'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Broken Bridge'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Broken Guide Post'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Broken IC Lead Guide'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Broken Latch'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Broken Lock'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Broken Positioning Pin'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Broken Post'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Broken Stand-Off'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Bubbles on Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Chipped-out Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Chipped-out Terminal'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Coilspring Distortion'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Contact Base Distortion'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Contact Distortion'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Contact Gap NG'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Contact Hammer Distortion'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Contact Lever Distortion'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Contact Protrussion'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Contact Sinking'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Corrosion'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Corrosion on Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Corrosion on Terminal'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Crack on #CV (Lock)'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Crack on Insulator'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Crack on Lock'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Crack on Latch'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Cut Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Cut Print'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Cut Terminal'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Damage on Top Point'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Damaged #SP'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Dent on #GP'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Dent on Bridge'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Dent on IC Lead Guide'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Dent on Insulator'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Discoloration on Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Dislocated Coilspring'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Double Print'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Excess Glue'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Excess Metal'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Excess Metal on Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Excess Metal on Terminal'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Excess Plastic'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Foreign Material'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Foreign Material (Lint Fiber)'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Foreign Material (Metal)'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Incomplete Insertion of Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Insufficient Glue'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Insufficient Pressing of Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Insufficient Pressing of Shaft'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Insufficient Pulling of Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Misaligned Bridge'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Misaligned Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Misaligned IC Lid'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Mis-insertion of Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Missing Coilspring'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Missing Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Missing Contact Hammer'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Missing E-ring'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Missing ME'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Missing Parts'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Missing Pin'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'No Glue'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Over Cut'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Overgrind'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Peeled-off Plating'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Peeled-off Plating (Contact)'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Peeled-off Plating (Terminal)'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Popped-up Base'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Popped-up Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Popped-up Contact Hammer'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Scratch on Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Short Hammer'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Short Pin'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Short Shot on Mold Part'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Shorted (HVT NG)'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Stain on Mold Part'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Stain on Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Stuck-up'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Stuck-up #LB'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Stuck-up Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Stuck-up Latch'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Stuck-up Lever'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Terminal Base Distortion'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Terminal Distortion'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Toolmark on Contact'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Toolmark on Contact Base'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Toolmark on Contact Hammer'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Toolmark on Contact Lever'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Toolmark on Terminal'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Twisted Terminal'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Unbalanced Optical Pin '
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Uncut Terminal'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Unlock #SD/#LB'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Unlocked #MO'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Weldline on Mold Part'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Wrong Cut'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Wrong Insertion'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Wrong Installation of Parts'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Wrong Location of Print'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Wrong Orientation of Parts'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Wrong Parts Used'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Wrong Pin Pattern'
        ]);
        mModRegistration::create([
            'family' => 'Burn In',
            'mod' => 'Wrong Print'
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
