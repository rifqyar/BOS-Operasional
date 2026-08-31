<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OperationCopyyard extends Model
{

    protected $table='operation_copyyards';



    protected $fillable=[
        'operation_id',
        'location_from_id',
        'location_to_id',
        'action_block',
        'status',
        'copyyard_at'
    ];



    protected $casts=[
        'copyyard_at'=>'datetime'
    ];



    public function operation()
    {
        return $this->belongsTo(
            Operation::class,
            'operation_id'
        );
    }



    public function locationFrom()
    {
        return $this->belongsTo(
            YardLocation::class,
            'location_from_id'
        );
    }



    public function locationTo()
    {
        return $this->belongsTo(
            YardLocation::class,
            'location_to_id'
        );
    }

}