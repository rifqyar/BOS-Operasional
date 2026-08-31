<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OperationDeliveryLog extends Model
{

    protected $table='operation_delivery_logs';


    public $timestamps=false;


    protected $fillable=[
        'operation_id',
        'delivery_id',
        'action',
        'status',
        'user_id',
        'note',
        'data_before',
        'data_after',
        'ip_address',
        'created_at'
    ];



    protected $casts=[
        'data_before'=>'array',
        'data_after'=>'array',
        'created_at'=>'datetime'
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



    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

}