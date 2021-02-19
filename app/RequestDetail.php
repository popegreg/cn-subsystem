<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestDetail extends Model
{
    protected $table = 'tbl_request_detail';

    protected $fillable = [
        'detailid',
        'code',
        'name',
        'classification',
        'issuedqty',
        'requestqty',
        'servedqty',
        'location',
        'remarks'
    ];
}
