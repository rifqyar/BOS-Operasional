<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContainerType extends Model
{

    protected $table = 'container_types';


    protected $fillable = [
        'code',
        'name',
        'size',
        'iso_code',
        'is_active'
    ];


    public function containers()
    {
        return $this->hasMany(
            Container::class,
            'container_type_id'
        );
    }


    public function behandleins()
    {
        return $this->hasMany(
            OperationBehandlein::class,
            'container_type_id'
        );
    }
}