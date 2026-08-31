<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OperationInspection extends Model
{

    protected $table = 'operation_inspections';


    protected $fillable = [
        'operation_id',
        'job_slip_id',
        'behandlein_id',
        'equipment_id',
        'operator_id',
        'no_seal',
        'container_type_id',
        'started_at',
        'finished_at',
        'status',
        'note'
    ];


    protected $casts = [
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


    public function behandlein()
    {
        return $this->belongsTo(
            OperationBehandlein::class,
            'behandlein_id'
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