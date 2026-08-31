<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OperationHold extends Model
{

    protected $table='operation_holds';



    protected $fillable=[
        'operation_id',
        'action',
        'reason',
        'user_id',
        'action_at'
    ];



    protected $casts=[
        'action_at'=>'datetime'
    ];



    public function operation()
    {
        return $this->belongsTo(
            Operation::class,
            'operation_id'
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