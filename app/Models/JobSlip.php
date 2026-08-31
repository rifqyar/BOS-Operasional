<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class JobSlip extends Model
{

    protected $table = 'job_slips';



    protected $fillable = [
        'spk_container_id',
        'no_job',
        'job_type',
        'status',
        'location_from_id',
        'location_to_id'
    ];



    public function spkContainer()
    {
        return $this->belongsTo(
            SpkContainer::class,
            'spk_container_id'
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



    public function details()
    {
        return $this->hasMany(
            JobDetail::class,
            'job_slip_id'
        );
    }



    public function behandles()
    {
        return $this->hasMany(
            OperationBehandlein::class,
            'job_slip_id'
        );
    }

}