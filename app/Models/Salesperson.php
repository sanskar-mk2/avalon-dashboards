<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salesperson extends Model
{
    public function sales()
    {
        return $this->hasMany(Sale::class, 'salesperson', 'salesman_no');
    }

    public function openOrders()
    {
        return $this->hasMany(OpenOrder::class, 'salesperson', 'salesman_no');
    }
}
