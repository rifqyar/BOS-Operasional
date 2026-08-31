<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OperationReeferLog extends Model
{

    protected $table='operation_reefer_logs';


    public $timestamps=false;


    protected $fillable=[
        'operation_id',
        'reefer_operation_id',
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



    public function reefer()
    {
        return $this->belongsTo(
            OperationReefer::class,
            'reefer_operation_id'
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