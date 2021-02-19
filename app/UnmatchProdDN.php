<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnmatchProdDN extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "unmatch_proddn";

    protected $fillable = [
        'code', 'name', 'drawing_num','r3_dn', 'error',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
}
