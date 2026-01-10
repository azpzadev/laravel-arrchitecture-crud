<?php

declare(strict_types=1);

namespace App\Infrastructure\Models;

use App\Domain\Customer\Enums\CustomerStatus;
use App\Domain\Shared\Traits\HasUuid;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected static function newFactory(): CustomerFactory
    {
        return CustomerFactory::new();
    }

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'phone',
        'address',
        'company',
        'status',
        'metadata',
    ];

    protected $casts = [
        'status' => CustomerStatus::class,
        'metadata' => 'array',
    ];

    protected $hidden = [
        'id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // public function orders(): HasMany
    // {
    //     return $this->hasMany(Order::class);
    // }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', CustomerStatus::Active);
    }

    public function scopeByStatus($query, CustomerStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'ilike', "%{$search}%")
              ->orWhere('email', 'ilike', "%{$search}%")
              ->orWhere('company', 'ilike', "%{$search}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === CustomerStatus::Active;
    }
}
