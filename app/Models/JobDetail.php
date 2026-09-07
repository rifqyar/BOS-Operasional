<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobDetail extends Model
{
    protected $table = 'job_details';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'job_slip_id',
        'equipment_id',
        'operator_id',
        'job_activity_code',
        'status',
        'job_activity_code_2',
        'truck_id',
        'operator_id_2',
        'job_activity_code_3',
        'equipment_id_3',
        'operator_id_3',
    ];

    protected $casts = [
        'id' => 'integer',
        'job_slip_id' => 'integer',
        'equipment_id' => 'integer',
        'operator_id' => 'integer',
        'truck_id' => 'integer',
        'operator_id_2' => 'integer',
        'equipment_id_3' => 'integer',
        'operator_id_3' => 'integer',
    ];

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

    public function operator2(): BelongsTo
    {
        return $this->belongsTo(
            SystemUser::class,
            'operator_id_2'
        );
    }

    public function equipment3(): BelongsTo
    {
        return $this->belongsTo(
            Equipment::class,
            'equipment_id_3'
        );
    }

    public function operator3(): BelongsTo
    {
        return $this->belongsTo(
            SystemUser::class,
            'operator_id_3'
        );
    }
}