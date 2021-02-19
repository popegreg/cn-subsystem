<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestSummary extends Model
{
    /**
     * [$table name of table to manage]
     * @var string
     */
    protected $table = "tbl_request_summary";

    /**
     * [$fillable fields that can be fill]
     * @var [array]
     */
    protected $fillable = [
                    'transno',
                    'pono',
                    'destination',
                    'line',
                    'status',
                    'requestedby',
                    'lastservedby',
                    'lastserveddate',
                    'createdby',
                    'updatedby'
                ];
}
