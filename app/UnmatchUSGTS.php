<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnmatchUSGTS extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "unmatch_usgts";

    protected $fillable = [
        'PO', 'productcode', 'productname','partcode','partname','supplier', 'kcode', 'error', 'lv' , 'usg', 'siyou', 'error_usg'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
}
