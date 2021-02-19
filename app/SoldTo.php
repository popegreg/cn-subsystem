<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SoldTo extends Model
{
    protected $table = "tbl_soldto";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'companyname', 'description'
    ];
}