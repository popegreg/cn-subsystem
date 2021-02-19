<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnmatchSales extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "unmatch_salesprice";

    protected $fillable = [
        'code','name','price', 'r3_price', 'error'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
}
