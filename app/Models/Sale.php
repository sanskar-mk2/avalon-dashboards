<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'company',
        'location',
        'order_no',
        'backorder',
        'order_date',
        'order_type',
        'customer_no',
        'customer_name',
        'customer_class',
        'brand',
        'flag',
        'salesperson',
        'invoice_no',
        'invoice_date',
        'item_no',
        'item_desc',
        'item_division',
        'inv_class',
        'qty',
        'ext_sales',
        'ext_cost',
        'period',
        'order_status',
        'advertising_source',
        'finance_co_rate',
        'price_matrix',
        'price_list_applied',
        'price_after_disc',
        'ship_to_no',
        'ship_to_name',
        'ship_to_city',
        'ship_to_state',
        'requested_ship_date',
        'customer_desire_date',
        'mfg_code'
    ];

    // protected $casts = [
    //     'invoice_date' => 'date',
    //     'period' => 'date',
    //     'qty' => 'float',
    //     'ext_sales' => 'float',
    //     'ext_cost' => 'float'
    // ];

    public function salespersonModel()
    {
        return $this->belongsTo(Salesperson::class, 'salesperson', 'salesman_no');
    }

    public function locationModel()
    {
        return $this->belongsTo(Location::class, 'location', 'location');
    }

    public function accountReceivables()
    {
        return $this->hasMany(AccountReceivable::class, 'customer_no', 'customer_no');
    }
}
