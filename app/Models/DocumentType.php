<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    protected $table = 'document_types';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'direction',
        'process_type',
        'autogate_hold',
        'autogate_doc_type',
        'permit_type',
        'npct1_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function spks(): HasMany
    {
        return $this->hasMany(Spk::class, 'document_type_id', 'id');
    }
}