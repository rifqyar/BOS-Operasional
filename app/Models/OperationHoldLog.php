<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OperationHoldLog extends Model
{

    protected $table='operation_hold_logs';


    public $timestamps=false;


    protected $fillable=[
        'operation_id',
        'hold_id',
        'action',
        'status',
        'reason',
        'user_id',
        'action_at',
        'data_before',
        'data_after',
        'ip_address',
        'created_at'
    ];


    protected $casts=[
        'data_before'=>'array',
        'data_after'=>'array',
        'action_at'=>'datetime',
        'created_at'=>'datetime'
    ];



    public function operation()
    {
        return $this->belongsTo(
            Operation::class,
            'operation_id'
        );
    }


    public function hold()
    {
        return $this->belongsTo(
            OperationHold::class,
            'hold_id'
        );
    }


    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

}