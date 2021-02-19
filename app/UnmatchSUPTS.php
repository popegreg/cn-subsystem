<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnmatchSUPTS extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "unmatch_supts";

    protected $fillable = [
        'partcode','partname','r3_sup', 'vendor', 'error'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
}
