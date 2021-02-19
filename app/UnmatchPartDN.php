<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnmatchPartDN extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "unmatch_partdn";

    protected $fillable = [
        'code', 'partname', 'drawing_num','r3_dn','error'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
}
