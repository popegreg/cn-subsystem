<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mDropdownCategory extends Model
{
   protected $table = "tbl_mdropdown_category";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category'
    ];
}
