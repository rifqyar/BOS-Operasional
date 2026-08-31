<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OperationMarshalling extends Model
{

    protected $table='operation_marshallings';



    protected $fillable=[
        'operation_id',
        'job_slip_id',
        'marshalling_type',
        'location_from_id',
        'location_to_id',
        'status',
        'started_at',
        'finished_at'
    ];



    protected $casts=[
        'started_at'=>'datetime',
        'finished_at'=>'datetime'
    ];



    public function operation()
    {
        return $this->belongsTo(
            Operation::class,
            'operation_id'
        );
    }



    public function jobSlip()
    {
        return $this->belongsTo(
            JobSlip::class,
            'job_slip_id'
        );
    }



    public function locationFrom()
    {
        return $this->belongsTo(
            YardLocation::class,
            'location_from_id'
        );
    }



    public function locationTo()
    {
        return $this->belongsTo(
            YardLocation::class,
            'location_to_id'
        );
    }

}