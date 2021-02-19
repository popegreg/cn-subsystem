<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackingList extends Model
{
     protected $table = "tbl_packinglist";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'soldto', 'carrier', 'portofloading', 'portofdestination', 'destinationofgoods','shipto'
    ];
}
