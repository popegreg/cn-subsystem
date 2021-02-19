<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ts_zypf0090 extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'id',
        'item_code',
        'item_text',
        'product_purchase_order',
        'item_number',
        'purchase_order_quantity',
        'statistical_delivery_date',
        'purchasing_delivery_date',
        'current_answer_time',
        'sales_order',
        'sales_order_specification',
        'proposed_response_date',
        'proposed_answer_time',
        'answer_quantity',
        'supplier_sector',
        'mrp_administrator',
        'issuing_storage_location',
        'planned_order_number',
        'production_orders',
        'purchase_order_number',
        'specification',
        'required_date',
        'proposed_division',
        'last_proposed_change_classification',
        'inventory_provisions_have_classification',
        'lock_change_classification',
        'vendor_code',
        'isDeleted'
    ];

    /**
    * Table name.
    */
    protected $table = 'ts_zypf0090';
}
