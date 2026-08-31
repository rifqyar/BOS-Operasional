<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class YardLocation extends Model
{

    protected $table='yard_locations';


    protected $fillable=[
        'block',
        'slot',
        'tier',
        'location_code',
        'is_occupied',
        'is_active'
    ];



    public function containers()
    {
        return $this->hasMany(
            Container::class,
            'current_location_id'
        );
    }



    public function jobsFrom()
    {
        return $this->hasMany(
            JobSlip::class,
            'location_from_id'
        );
    }



    public function jobsTo()
    {
        return $this->hasMany(
            JobSlip::class,
            'location_to_id'
        );
    }

}