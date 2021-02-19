<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnmatchPartName extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "unmatch_partname";

    protected $fillable = [
        'code', 'partname', 'r3_partname','error',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
}
