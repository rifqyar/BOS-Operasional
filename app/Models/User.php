<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable // implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'reff_user';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'USER_NAME',
        'PASS',
        'NAMA',
        'NOTELP',
        'EMAIL',
        'KD_GA',
        'KD_TPS',
        'KD_GUDANG',
        'KD_GROUP',
        'STATUS',
        'ROLE',
        'LAST_LOGIN',
        'WK_REKAM',
        'NPWP',
        'PASS_BOSBARU',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'PASS',
        'PASS_BOSBARU',
    ];

    protected function casts(): array
    {
        return [
            'ID'         => 'integer',
            'LAST_LOGIN' => 'datetime',
            'WK_REKAM'   => 'datetime',
        ];
    }

    public function getAuthPassword(): string
    {
        return (string) ($this->attributes['PASS'] ?? '');
    }

    public function getNameAttribute(): string
    {
        return (string) ($this->attributes['NAMA'] ?? '');
    }

    public function getEmailAttribute(): string
    {
        return (string) ($this->attributes['EMAIL'] ?? '');
    }

    public function getUsernameAttribute(): string
    {
        return (string) ($this->attributes['USER_NAME'] ?? '');
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }
}
