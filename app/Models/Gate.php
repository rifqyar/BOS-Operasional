<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Gate extends Model
{

    protected $table='gates';


    protected $fillable=[
        'code',
        'name',
        'is_active'
    ];



    public function deliveries()
    {
        return $this->hasMany(
            OperationDelivery::class,
            'gate_id'
        );
    }

}