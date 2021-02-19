<?php
/*******************************************************************************
     Copyright (c) <Company Name> All rights reserved.

     FILE NAME: mJustification.php
     MODULE NAME:  [2004] Jsutification Master
     CREATED BY: MESPINOSA
     DATE CREATED: 2016.04.14
     REVISION HISTORY :

     VERSION     ROUND    DATE           PIC          DESCRIPTION
     100-00-01   1     2016.04.14     MESPINOSA       Initial Draft
*******************************************************************************/
?>
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
* Justification Model
*/
class mJustification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'code', 'name', 'create_pg', 'create_user', 
        'create_date', 'update_pg', 'update_user', 'update_date'
    ];

    /**
    * Table name.
    */
    protected $table = 'mjustifications';
}