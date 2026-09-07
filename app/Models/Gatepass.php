<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gatepass extends Model
{
    protected $table = 'gatepasses';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'gate_id',
        'spk_id',
        'spk_container_id',
        'no_dok',
        'jenis_dokumen',
        'jenis_kegiatan',
        'status',
        'requested_at',
        'received_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'gate_id' => 'integer',
        'spk_id' => 'integer',
        'spk_container_id' => 'integer',
        'requested_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function gate(): BelongsTo
    {
        return $this->belongsTo(
            Gate::class,
            'gate_id'
        );
    }

    public function spk(): BelongsTo
    {
        return $this->belongsTo(
            Spk::class,
            'spk_id'
        );
    }

    public function spkContainer(): BelongsTo
    {
        return $this->belongsTo(
            SpkContainer::class,
            'spk_container_id'
        );
    }
}