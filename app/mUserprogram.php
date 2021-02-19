<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mUserprogram extends Model
{
    // protected $connection = 'mysql';
    protected $table = "muserprograms";

    protected $fillable = [
        'program_code','user_id','id_tblusers','program_name','read_write','create_pg','create_user'
    ];

    public function user()
    {
    	//This for the relationship
    	return $this->belongsTo('App\User');
    }

    public function program()
    {
    	//This for the relationship
    	return $this->belongsTo('App\mProgram');
    }
}
