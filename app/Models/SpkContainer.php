<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SpkContainer extends Model
{

    protected $table = 'spk_containers';


    protected $fillable = [
        'spk_id',
        'container_id',
        'status',
        'fl_hold',
        'fl_warna_hold',
        'fl_send_npct1'
    ];



    public function spk()
    {
        return $this->belongsTo(
            Spk::class,
            'spk_id'
        );
    }



    public function container()
    {
        return $this->belongsTo(
            Container::class,
            'container_id'
        );
    }



    public function jobSlips()
    {
        return $this->hasMany(
            JobSlip::class,
            'spk_container_id'
        );
    }

}