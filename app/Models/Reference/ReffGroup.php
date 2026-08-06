<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReffGroup extends Model
{
    use HasFactory;

    protected $table = 'reff_group';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ID',
        'NAMA',
    ];
}
