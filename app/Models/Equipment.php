<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Equipment extends Model
{

    protected $table='equipments';


    protected $fillable=[
        'code',
        'name',
        'is_active'
    ];



    public function jobDetails()
    {
        return $this->hasMany(
            JobDetail::class,
            'equipment_id'
        );
    }

}