<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OperationBehandlein extends Model
{

    protected $table='operation_behandleins';


    protected $fillable=[
        'operation_id',
        'job_slip_id',
        'equipment_id',
        'operator_id',
        'no_seal',
        'container_type_id',
        'join_inspection',
        'started_at',
        'finished_at',
        'status',
        'note'
    ];



    protected $casts=[
        'join_inspection'=>'boolean',
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



    public function equipment()
    {
        return $this->belongsTo(
            Equipment::class,
            'equipment_id'
        );
    }



    public function operator()
    {
        return $this->belongsTo(
            User::class,
            'operator_id'
        );
    }



    public function containerType()
    {
        return $this->belongsTo(
            ContainerType::class,
            'container_type_id'
        );
    }

}