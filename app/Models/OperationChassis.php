<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OperationChassis extends Model
{

    protected $table='operation_chassis';



    protected $fillable=[
        'operation_id',
        'truck_id',
        'location_id',
        'chassis_at',
        'status'
    ];



    protected $casts=[
        'chassis_at'=>'datetime'
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



    public function location()
    {
        return $this->belongsTo(
            YardLocation::class,
            'location_id'
        );
    }

}