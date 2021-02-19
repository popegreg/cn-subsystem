<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProdDestination extends Model
{
   protected $table = "tbl_prod_destination";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description'
    ];
}
