<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpenOrder extends Model
{
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
