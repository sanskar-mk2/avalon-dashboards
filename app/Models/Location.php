<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public function sales()
    {
        return $this->hasMany(Sale::class, 'location', 'location');
    }

    public function openOrders()
    {
        return $this->hasMany(OpenOrder::class, 'location', 'location');
    }

    public function accountReceivables()
    {
        return $this->hasMany(AccountReceivable::class, 'location', 'location');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'location', 'location');
    }
}
