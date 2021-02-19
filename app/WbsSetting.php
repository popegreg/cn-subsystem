<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WbsSetting extends Model
{
    protected $table = "tbl_wbssetting";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'value'
    ];
}
