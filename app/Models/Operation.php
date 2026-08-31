<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Operation extends Model
{

    protected $table = 'operations';



    protected $fillable = [
        'spk_id',
        'container_id',
        'current_process',
        'status',
        'created_by',
        'updated_by',
        'started_at',
        'finished_at'
    ];



    protected $casts = [
        'started_at'=>'datetime',
        'finished_at'=>'datetime'
    ];



    public function spk()
    {
        return $this->belongsTo(
            Spk::class,
            'spk_id'
        );
    }



    public function container()
    {
        return $this->belongsTo(
            Container::class,
            'container_id'
        );
    }



    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }



    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }



    public function pickup()
    {
        return $this->hasOne(
            OperationPickup::class,
            'operation_id'
        );
    }



    public function behandlein()
    {
        return $this->hasOne(
            OperationBehandlein::class,
            'operation_id'
        );
    }



    public function hold()
    {
        return $this->hasOne(
            OperationHold::class,
            'operation_id'
        );
    }



    public function marshalling()
    {
        return $this->hasOne(
            OperationMarshalling::class,
            'operation_id'
        );
    }



    public function inspection()
    {
        return $this->hasOne(
            OperationInspection::class,
            'operation_id'
        );
    }



    public function reefer()
    {
        return $this->hasOne(
            OperationReefer::class,
            'operation_id'
        );
    }



    public function delivery()
    {
        return $this->hasOne(
            OperationDelivery::class,
            'operation_id'
        );
    }



    public function inspectionOut()
    {
        return $this->hasOne(
            OperationInspectionOut::class,
            'operation_id'
        );
    }



    public function chassis()
    {
        return $this->hasOne(
            OperationChassis::class,
            'operation_id'
        );
    }



    public function copyyard()
    {
        return $this->hasOne(
            OperationCopyyard::class,
            'operation_id'
        );
    }

    public function pickupLogs()
{
    return $this->hasMany(
        OperationPickupLog::class,
        'operation_id'
    );
}


public function behandleinLogs()
{
    return $this->hasMany(
        OperationBehandleinLog::class,
        'operation_id'
    );
}


public function holdLogs()
{
    return $this->hasMany(
        OperationHoldLog::class,
        'operation_id'
    );
}


public function marshallingLogs()
{
    return $this->hasMany(
        OperationMarshallingLog::class,
        'operation_id'
    );
}

public function inspectionLogs()
{
    return $this->hasMany(
        OperationInspectionLog::class,
        'operation_id'
    );
}


public function reeferLogs()
{
    return $this->hasMany(
        OperationReeferLog::class,
        'operation_id'
    );
}


public function reeferMonitoringLogs()
{
    return $this->hasMany(
        OperationReeferMonitoringLog::class,
        'operation_id'
    );
}


public function deliveryLogs()
{
    return $this->hasMany(
        OperationDeliveryLog::class,
        'operation_id'
    );
}


public function inspectionOutLogs()
{
    return $this->hasMany(
        OperationInspectionOutLog::class,
        'operation_id'
    );
}


public function chassisLogs()
{
    return $this->hasMany(
        OperationChassisLog::class,
        'operation_id'
    );
}


public function copyyardLogs()
{
    return $this->hasMany(
        OperationCopyyardLog::class,
        'operation_id'
    );
}

}