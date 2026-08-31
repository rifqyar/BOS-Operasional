<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class JobDetail extends Model
{

    protected $table = 'job_details';



    protected $fillable = [
        'job_slip_id',
        'equipment_id',
        'operator_id',
        'status'
    ];



    public function jobSlip()
    {
        return $this->belongsTo(
            JobSlip::class,
            'job_slip_id'
        );
    }



    public function equipment()
    {
        return $this->belongsTo(
            Equipment::class,
            'equipment_id'
        );
    }



    public function operator()
    {
        return $this->belongsTo(
            User::class,
            'operator_id'
        );
    }

}