<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ts_mrp extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mcode',
        'mname',
        'vendor',
        'assy100',
        'assy102',
        'whs100',
        'whs102',
        'whs106',
        'whs_sm',
        'whs_non',
        'ttlcurrinvtry',
        'orddate',
        'duedate',
        'po',
        'dcode',
        'dname',
        'orderqty',
        'orderbal',
        'custcode',
        'custname',
        'schdqty',
        'balreq',
        'ttlbalreq',
        'reqaccum',
        'alloccalc',
        'ttlpr_bal',
        'mrp',
        'pr_issued',
        'pr',
        'yec_po',
        'yec_pu',
        'flight',
        'deliqty',
        'deliaccum',
        'check',
        'supcode',
        'supname',
        're'
        'status'
        'isDeleted',
    ];

    /**
    * Table name.
    */
    protected $table = 'ts_mrp';
}
