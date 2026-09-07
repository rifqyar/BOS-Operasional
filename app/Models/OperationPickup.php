<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationPickup extends Model
{
    protected $table = 'operation_pickups';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'operation_id',
        'truck_id',
        'status',
        'pickup_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'operation_id' => 'integer',
        'truck_id' => 'integer',
        'pickup_at' => 'datetime',
    ];

    public function operation()
    {
        return $this->belongsTo(
            Operation::class,
            'operation_id'
        );
    }

    public function truck()
    {
        return $this->belongsTo(
            Truck::class,
            'truck_id'
        );
    }
}