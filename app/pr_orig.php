<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pr_orig extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'sales_no',
            'sales_type',
            'sales_org',
            'commercial',
            'section',
            'sales_branch',
            'sales_g',
            'supplier',
            'destination',
            'player',
            'assistant',
            'po_num',
            'issued_date',
            'flight_need_date',
            'headertext',
            'pcode',
            'itemtext',
            'orderqty',
            'unit',
    ];

    /**
    * Table name.
    */
    protected $table = 'pr_orig';
}
