<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountReceivable extends Model
{
    protected $appends = ['customer_name'];

    public function locationModel()
    {
        return $this->belongsTo(Location::class, 'location', 'location');
    }

    public function sales()
    {
        return $this->belongsTo(Sale::class, 'customer_no', 'customer_no');
    }

    public function openOrders()
    {
        return $this->belongsTo(OpenOrder::class, 'customer_no', 'customer_no');
    }

    public function getCustomerNameAttribute()
    {
        return $this->sales()->first()?->customer_name ?? $this->openOrders()->first()?->customer_name;
    }
}
