<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OperationInspectionOut extends Model
{

    protected $table='operation_inspection_outs';



    protected $fillable=[
        'operation_id',
        'delivery_id',
        'seal_condition',
        'no_seal',
        'container_condition_id',
        'inspected_by',
        'inspected_at',
        'status',
        'note'
    ];



    protected $casts=[
        'inspected_at'=>'datetime'
    ];



    public function operation()
    {
        return $this->belongsTo(
            Operation::class,
            'operation_id'
        );
    }



    public function delivery()
    {
        return $this->belongsTo(
            OperationDelivery::class,
            'delivery_id'
        );
    }



    public function condition()
    {
        return $this->belongsTo(
            ContainerCondition::class,
            'container_condition_id'
        );
    }



    public function inspector()
    {
        return $this->belongsTo(
            User::class,
            'inspected_by'
        );
    }

}