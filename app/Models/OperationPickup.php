<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OperationPickup extends Model
{

    protected $table='operation_pickups';


    protected $fillable=[
        'operation_id',
        'truck_id',
        'status',
        'pickup_at'
    ];



    protected $casts=[
        'pickup_at'=>'datetime'
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