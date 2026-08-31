<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OperationReeferMonitoringLog extends Model
{

    protected $table='operation_reefer_monitoring_logs';


    public $timestamps=false;


    protected $fillable=[
        'operation_id',
        'reefer_operation_id',
        'reefer_monitoring_id',
        'action',
        'status',
        'temperature',
        'set_temperature',
        'voltage',
        'ampere',
        'alarm',
        'note',
        'monitored_by',
        'monitored_at',
        'user_id',
        'ip_address',
        'created_at'
    ];



    protected $casts=[
        'monitored_at'=>'datetime',
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



    public function monitoring()
    {
        return $this->belongsTo(
            ReeferMonitoring::class,
            'reefer_monitoring_id'
        );
    }



    public function monitor()
    {
        return $this->belongsTo(
            User::class,
            'monitored_by'
        );
    }

}