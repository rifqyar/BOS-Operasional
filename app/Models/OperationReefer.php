<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OperationReefer extends Model
{

    protected $table='operation_reefers';


    protected $fillable=[
        'operation_id',
        'plugin_at',
        'unplug_at',
        'status'
    ];



    protected $casts=[
        'plugin_at'=>'datetime',
        'unplug_at'=>'datetime'
    ];



    public function operation()
    {
        return $this->belongsTo(
            Operation::class,
            'operation_id'
        );
    }



    public function monitorings()
    {
        return $this->hasMany(
            ReeferMonitoring::class,
            'reefer_operation_id'
        );
    }

}