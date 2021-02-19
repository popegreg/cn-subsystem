<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mProductline extends Model
{
	//protected $connection = 'mysql';
    protected $table = "mproductlines";
    protected $fillable = [
        'code','name','create_user'
    ];
}
