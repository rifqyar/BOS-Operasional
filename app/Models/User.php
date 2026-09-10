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

    protected $primaryKey = 'id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_name',
        'pass',
        'nama',
        'notelp',
        'email',
        'kd_ga',
        'kd_tps',
        'kd_gudang',
        'kd_group',
        'status',
        'role',
        'last_login',
        'wk_rekam',
        'npwp',
        'pass_bosbaru',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'pass',
        'pass_bosbaru',
    ];

    protected function casts(): array
    {
        return [
            'id'         => 'integer',
            'last_login' => 'datetime',
            'wk_rekam'   => 'datetime',
        ];
    }

    public function getAuthPassword(): string
    {
        return (string) ($this->attributes['pass'] ?? '');
    }

    public function getNameAttribute(): string
    {
        return (string) ($this->attributes['nama'] ?? '');
    }

    public function getEmailAttribute(): string
    {
        return (string) ($this->attributes['email'] ?? '');
    }

    public function getUsernameAttribute(): string
    {
        return (string) ($this->attributes['user_name'] ?? '');
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }
}
