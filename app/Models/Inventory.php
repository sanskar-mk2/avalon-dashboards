<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    public function locationModel()
    {
        return $this->belongsTo(Location::class, 'location', 'location');
    }
}
