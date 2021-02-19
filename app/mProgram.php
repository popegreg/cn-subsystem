<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mProgram extends Model
{
 //    protected $connection = 'mysql';
	protected $table = "mprograms";
	
	protected $fillable = [
        'program_code','program_name','program_class','program_description','create_user'
    ];

    public function user()
    {
    	//This for the relationship
    	return $this->belongsTo('App\User');
    }
    
    public function userprogram()
    {
        return $this->hasMany('App\mUserprogram');
    }
}
