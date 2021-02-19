<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LineDestination extends Model
{
   protected $table = "tbl_line_destination";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description'
    ];
}
