<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ReeferMonitoring extends Model
{

    protected $table='reefer_monitorings';



    protected $fillable=[
        'reefer_operation_id',
        'temperature',
        'set_temperature',
        'voltage',
        'ampere',
        'alarm',
        'note',
        'monitored_by',
        'monitored_at'
    ];



    protected $casts=[
        'monitored_at'=>'datetime'
    ];



    public function reefer()
    {
        return $this->belongsTo(
            OperationReefer::class,
            'reefer_operation_id'
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