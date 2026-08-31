<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OperationInspectionOutLog extends Model
{

    protected $table='operation_inspection_out_logs';


    public $timestamps=false;


    protected $fillable=[
        'operation_id',
        'inspection_out_id',
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



    public function inspectionOut()
    {
        return $this->belongsTo(
            OperationInspectionOut::class,
            'inspection_out_id'
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