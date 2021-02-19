<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
   // protected $connection = 'sqlsrv';
    //protected $table = "users";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'lastname', 'firstname','middlename','password','productline','actual_password', 'create_pg'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function userprogram()
    {
        return $this->hasMany('App\Userprogram');
    }
}
