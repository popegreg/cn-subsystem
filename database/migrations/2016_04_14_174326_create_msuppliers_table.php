<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\mSupplier;

class CreateMsuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('msuppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',20);
            $table->string('name',200);
            $table->text('address',500)->nullable();
            $table->string('tel_no',50)->nullable();
            $table->string('fax_no',50)->nullable();
            $table->string('email',50)->nullable();
            $table->string('create_pg', 50)->nullable();
            $table->string('create_user', 20)->nullable();
            $table->string('update_pg', 50)->nullable();
            $table->string('update_user', 20)->nullable();
            $table->index('code');
            $table->timestamps();
        });

        mSupplier::create([
            'code' => 'YEC',
            'name' => 'YEC',
            'create_pg' => '2002',
            'create_user' => 'admin'
        ]);

        mSupplier::create([
            'code' => 'ASSY100',
            'name' => 'ASSY100',
            'create_pg' => '2002',
            'create_user' => 'admin'
        ]);

        mSupplier::create([
            'code' => 'AYE',
            'name' => 'AYE',
            'create_pg' => '2002',
            'create_user' => 'admin'
        ]);

        mSupplier::create([
            'code' => 'PAPTI',
            'name' => 'PAPTI',
            'create_pg' => '2002',
            'create_user' => 'admin'
        ]);

        mSupplier::create([
            'code' => 'PPD',
            'name' => 'PPD',
            'create_pg' => '2002',
            'create_user' => 'admin'
        ]);

        mSupplier::create([
            'code' => 'PPDG',
            'name' => 'PPDG',
            'create_pg' => '2002',
            'create_user' => 'admin'
        ]);

        mSupplier::create([
            'code' => 'PURH100',
            'name' => 'PURH100',
            'create_pg' => '2002',
            'create_user' => 'admin'
        ]);

        mSupplier::create([
            'code' => 'SIIX',
            'name' => 'SIIX',
            'create_pg' => '2002',
            'create_user' => 'admin'
        ]);

        mSupplier::create([
            'code' => 'WHS100',
            'name' => 'WHS100',
            'create_pg' => '2002',
            'create_user' => 'admin'
        ]);

        mSupplier::create([
            'code' => 'YEU ASSY100',
            'name' => 'YEU ASSY100',
            'create_pg' => '2002',
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
        Schema::drop('msuppliers');
    }
}
