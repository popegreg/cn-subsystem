<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnmatchBom extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "unmatch_bom";

    protected $fillable = [
        'code','partcode','partname','r3_code','r3_partcode','r3_partname','error',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
}
