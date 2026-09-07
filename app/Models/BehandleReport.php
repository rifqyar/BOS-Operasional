<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BehandleReport extends Model
{
    protected $table = 'behandle_reports';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'operation_behandlein_id',
        'gatepass_id',
        'no_spk',
        'no_cont',
        'no_dok',
        'behandle_in',
        'kondisi_cont',
        'no_seal',
        'iso_code',
        'lokasi',
        'pb1_marshalling_b1',
        'pb1_lokasi_cic',
        'pb2_marshalling_b2',
        'pb2_lokasi_cic',
    ];

    protected $casts = [
        'id' => 'integer',
        'operation_behandlein_id' => 'integer',
        'gatepass_id' => 'integer',
        'behandle_in' => 'datetime',
        'pb1_marshalling_b1' => 'datetime',
        'pb2_marshalling_b2' => 'datetime',
    ];

    public function operationBehandlein(): BelongsTo
    {
        return $this->belongsTo(
            OperationBehandlein::class,
            'operation_behandlein_id'
        );
    }

    public function gatepass(): BelongsTo
    {
        return $this->belongsTo(
            Gatepass::class,
            'gatepass_id'
        );
    }
}