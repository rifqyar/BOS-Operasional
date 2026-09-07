<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemUser extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'username',
        'name',
        'role',
        'is_active',
    ];

    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function jobDetails(): HasMany
    {
        return $this->hasMany(JobDetail::class, 'operator_id');
    }

    public function operationBehandleins(): HasMany
    {
        return $this->hasMany(OperationBehandlein::class, 'operator_id');
    }
}