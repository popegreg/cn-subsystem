<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mSeriesRegistration extends Model
{
    protected $table = "tbl_seriesregistration";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'family','series'
    ];
}
