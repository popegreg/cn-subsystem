<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
   protected $table = "tbl_transaction";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'description', 'prefix','prefixformat','nextno','nextnolength'
    ];
}
