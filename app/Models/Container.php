<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $table = 'containers';


    protected $fillable = [
        'no_cont',
        'container_type_id',
        'current_status_id',
        'current_location_id',
        'no_seal',
        'fl_dg',
        'fl_oog',
        'imo',
        'is_active'
    ];


    public function type()
    {
        return $this->belongsTo(
            ContainerType::class,
            'container_type_id'
        );
    }


    public function status()
    {
        return $this->belongsTo(
            ContainerStatus::class,
            'current_status_id'
        );
    }


    public function location()
    {
        return $this->belongsTo(
            YardLocation::class,
            'current_location_id'
        );
    }


    public function operations()
    {
        return $this->hasMany(
            Operation::class,
            'container_id'
        );
    }


    public function spkContainers()
    {
        return $this->hasMany(
            SpkContainer::class,
            'container_id'
        );
    }
}