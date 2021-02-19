<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OqcInspectionMoldingMOD extends Model
{
    protected $table = "tbl_oqc_molding_mod";

    protected $fillable = ['po','partcode','description','qty'];
}
