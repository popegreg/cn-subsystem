<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mDropdownSeries extends Model
{
    protected $table = "tbl_dropdown_series";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'BGA','BGA-FP','LGA','PGA','PGA-LGA','Probe Pin','PUS','QFN','QFP1','QFP2','Socket','SOJ','SON','TSOP'
    ];
}
