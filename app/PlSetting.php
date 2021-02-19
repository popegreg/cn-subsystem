<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlSetting extends Model
{
    protected $table = "tbl_packinglist_setting";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'assign', 'user', 'prodline'
    ];
}
