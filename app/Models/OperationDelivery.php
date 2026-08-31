<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OperationDelivery extends Model
{

    protected $table='operation_deliveries';



    protected $fillable=[
        'operation_id',
        'truck_id',
        'gate_id',
        'truck_in_at',
        'chassis_at',
        'inspect_at',
        'gate_out_at',
        'status'
    ];



    protected $casts=[
        'truck_in_at'=>'datetime',
        'chassis_at'=>'datetime',
        'inspect_at'=>'datetime',
        'gate_out_at'=>'datetime'
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



    public function gate()
    {
        return $this->belongsTo(
            Gate::class,
            'gate_id'
        );
    }

}