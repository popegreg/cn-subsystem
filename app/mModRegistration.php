<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mModRegistration extends Model
{
    protected $table = "tbl_modregistration";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mod','family'
    ];
}
