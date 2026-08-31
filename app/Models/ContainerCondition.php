<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ContainerCondition extends Model
{

    protected $table = 'container_conditions';


    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active'
    ];


    public function inspectionOuts()
    {
        return $this->hasMany(
            OperationInspectionOut::class,
            'container_condition_id'
        );
    }

}