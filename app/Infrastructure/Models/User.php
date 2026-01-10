<?php

declare(strict_types=1);

namespace App\Infrastructure\Models;

use App\Domain\Shared\Traits\HasUuid;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuid, Notifiable;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    protected $fillable = [
        'uuid',
        'name',
        'username',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByUsername($query, string $username)
    {
        return $query->where('username', $username);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->is_active === true;
    }
}
