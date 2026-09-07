<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationMarshallingLog extends Model
{
    protected $table = 'operation_marshalling_logs';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'operation_id',
        'marshalling_id',
        'action',
        'status',
        'user_id',
        'note',
        'data_before',
        'data_after',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'operation_id' => 'integer',
        'marshalling_id' => 'integer',
        'user_id' => 'integer',
        'data_before' => 'array',
        'data_after' => 'array',
        'created_at' => 'datetime',
    ];

    public function operation(): BelongsTo
    {
        return $this->belongsTo(
            Operation::class,
            'operation_id'
        );
    }

    public function marshalling(): BelongsTo
    {
        return $this->belongsTo(
            OperationMarshalling::class,
            'marshalling_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            SystemUser::class,
            'user_id'
        );
    }
}