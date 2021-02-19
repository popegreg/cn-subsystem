<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class timpzymr0120 extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'itemcode',
        'name',
        'orderno',
        'schdqty',
        'ppd_reply',
        'remarks',
        'isDeleted',
    ];

    /**
    * Table name.
    */
    protected $table = 'timpzymr0120';
}
