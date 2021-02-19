<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\PackageCategory;

class CreateTblPackageCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_package_category', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description',20)->nullable();
            $table->string('create_user', 20)->nullable();
            $table->string('update_user', 20)->nullable();
            $table->timestamps();
        });

        PackageCategory::create([
            'description' => 'Box',
            'update_user' => 'admin',
            'create_user' => 'admin'
        ]);
        PackageCategory::create([
            'description' => 'Plastic',
            'update_user' => 'admin',
            'create_user' => 'admin'
        ]);
        PackageCategory::create([
            'description' => 'Roll',
            'update_user' => 'admin',
            'create_user' => 'admin'
        ]);
        PackageCategory::create([
            'description' => 'Pack',
            'update_user' => 'admin',
            'create_user' => 'admin'
        ]);
        PackageCategory::create([
            'description' => 'Bundle',
            'update_user' => 'admin',
            'create_user' => 'admin'
        ]);
        PackageCategory::create([
            'description' => 'Reel',
            'update_user' => 'admin',
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
        Schema::drop('tbl_package_category');
    }
}
