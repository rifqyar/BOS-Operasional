<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobSlip extends Model
{
    protected $table = 'job_slips';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'spk_container_id',
        'gatepass_id',
        'no_job',
        'job_type',
        'status',
        'location_from_id',
        'location_to_id',
        'note',
        'status_fumigasi',
    ];

    protected $casts = [
        'id' => 'integer',
        'spk_container_id' => 'integer',
        'gatepass_id' => 'integer',
        'location_from_id' => 'integer',
        'location_to_id' => 'integer',
    ];

    public function spkContainer(): BelongsTo
    {
        return $this->belongsTo(
            SpkContainer::class,
            'spk_container_id'
        );
    }

    public function gatepass(): BelongsTo
    {
        return $this->belongsTo(
            Gatepass::class,
            'gatepass_id'
        );
    }

    public function locationFrom(): BelongsTo
    {
        return $this->belongsTo(
            YardLocation::class,
            'location_from_id'
        );
    }

    public function locationTo(): BelongsTo
    {
        return $this->belongsTo(
            YardLocation::class,
            'location_to_id'
        );
    }

    public function details(): HasMany
    {
        return $this->hasMany(
            JobDetail::class,
            'job_slip_id'
        );
    }

    public function behandles(): HasMany
    {
        return $this->hasMany(
            OperationBehandlein::class,
            'job_slip_id'
        );
    }
}