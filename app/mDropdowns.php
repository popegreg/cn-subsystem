<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mDropdowns extends Model
{
   protected $table = "tbl_mdropdowns";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description','category'
    ];
}
