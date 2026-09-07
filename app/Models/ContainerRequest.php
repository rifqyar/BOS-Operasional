<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContainerRequest extends Model
{
    protected $table = 'container_requests';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'spk_container_id',
        'container_type_id',
        'container_size',
        'container_type_code',
        'container_kind',
        'fl_perbaiki',
        'fl_dg',
        'status_dg',
        'status_billing',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'spk_container_id' => 'integer',
        'container_type_id' => 'integer',
    ];

    public function spkContainer(): BelongsTo
    {
        return $this->belongsTo(
            SpkContainer::class,
            'spk_container_id'
        );
    }

    public function containerType(): BelongsTo
    {
        return $this->belongsTo(
            ContainerType::class,
            'container_type_id'
        );
    }
}