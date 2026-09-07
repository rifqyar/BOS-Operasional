<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationBehandlein extends Model
{
    protected $table = 'operation_behandleins';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'operation_id',
        'job_slip_id',
        'equipment_id',
        'operator_id',
        'truck_id',
        'no_seal',
        'container_type_id',
        'container_condition_id',
        'iso_code',
        'load_status',
        'location_id',
        'join_inspection',
        'label',
        'started_at',
        'finished_at',
        'status',
        'note',
    ];

    protected $casts = [
        'id' => 'integer',
        'operation_id' => 'integer',
        'job_slip_id' => 'integer',
        'equipment_id' => 'integer',
        'operator_id' => 'integer',
        'truck_id' => 'integer',
        'container_type_id' => 'integer',
        'container_condition_id' => 'integer',
        'location_id' => 'integer',
        'join_inspection' => 'boolean',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function operation(): BelongsTo
    {
        return $this->belongsTo(
            Operation::class,
            'operation_id'
        );
    }

    public function jobSlip(): BelongsTo
    {
        return $this->belongsTo(
            JobSlip::class,
            'job_slip_id'
        );
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(
            Equipment::class,
            'equipment_id'
        );
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(
            SystemUser::class,
            'operator_id'
        );
    }

    public function truck(): BelongsTo
    {
        return $this->belongsTo(
            Truck::class,
            'truck_id'
        );
    }

    public function containerType(): BelongsTo
    {
        return $this->belongsTo(
            ContainerType::class,
            'container_type_id'
        );
    }

    public function containerCondition(): BelongsTo
    {
        return $this->belongsTo(
            ContainerCondition::class,
            'container_condition_id'
        );
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(
            YardLocation::class,
            'location_id'
        );
    }
}