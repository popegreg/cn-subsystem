<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\User;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id',20)->unique();
            $table->string('lastname', 50);
            $table->string('firstname', 50);
            $table->string('middlename', 50);
            $table->string('password', 70);
            $table->string('productline', 10);
            $table->string('actual_password', 20);
            $table->enum('locked', ['0', '1'])->default('0');
            $table->dateTime('last_date_loggedin')->nullable();
            $table->string('create_pg', 50)->default('0');
            $table->string('create_user', 20)->default('0');
            $table->string('update_pg', 50)->nullable();
            $table->string('update_user', 20)->nullable();
            $table->enum('delete_flag', ['0','1'])->default('0');
            $table->rememberToken();
            $table->timestamps();
            $table->index('user_id');
        });

        User::create([
            'user_id' => 'admin',
            'lastname' => 'SITP',
            'firstname' => 'Administrator',
            'middlename' => 'Seiko',
            'password' => '$2y$10$Mj5nKk6rILRIhJiQ79PExuFQRbFnEDcq5lJxHSGckT474pBYB/vKW',
            'productline' => 'TS',
            'actual_password' => '@dmin',
            'locked' => 0,
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
        Schema::drop('users');
    }
}
