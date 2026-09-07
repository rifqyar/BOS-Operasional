<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spk extends Model
{
    protected $table = 'spks';

    protected $fillable = [
        'no_spk',
        'no_dok',
        'tgl_dok',
        'status',
    ];

    public function containers()
    {
        return $this->hasMany(
            SpkContainer::class,
            'spk_id'
        );
    }

    public function operations()
    {
        return $this->hasMany(
            Operation::class,
            'spk_id'
        );
    }
}