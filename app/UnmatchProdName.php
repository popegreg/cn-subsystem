<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnmatchProdName extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "unmatch_prodname";

    protected $fillable = [
        'code', 'name', 'r3_name','error','drawing_num','r3_dn', 'error_dn', 'bu'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
}
