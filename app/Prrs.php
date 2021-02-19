<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: Prrs.php
     MODULE NAME:  [3004] PRRS
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.04.28
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.04.28     MESPINOSA       Initial Draft
*******************************************************************************/
?>
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
* Prrs Model
*/
class Prrs extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
          'last_update'
        , 'period_covered'
        , 'standard1'
        ,'lower_limit_price'
        ,'standard2'
        ,'for_gr_po'
    ];

/*    public function classifications()
    {
        return $this->hasMany('App\Classifications');
    }*/


    /**
    * Table name.
    */
    protected $table = 'prrs';
}
