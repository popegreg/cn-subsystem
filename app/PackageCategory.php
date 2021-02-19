<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageCategory extends Model
{
    protected $table = "tbl_package_category";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'description'
    ];
}
