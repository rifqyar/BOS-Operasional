<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    protected $table = 'trucks';

    protected $fillable = [];

    public function pickups()
    {
        return $this->hasMany(
            OperationPickup::class,
            'truck_id'
        );
    }

    public function deliveries()
    {
        return $this->hasMany(
            OperationDelivery::class,
            'truck_id'
        );
    }
}