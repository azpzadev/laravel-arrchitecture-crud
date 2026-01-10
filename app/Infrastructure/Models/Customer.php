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

/**
 * Customer model representing client entities.
 *
 * Supports soft deletes, UUID identification, and
 * status-based filtering.
 *
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $company
 * @property CustomerStatus $status
 * @property array $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Customer extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     *
     * @return CustomerFactory
     */
    protected static function newFactory(): CustomerFactory
    {
        return CustomerFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => CustomerStatus::class,
        'metadata' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
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

    /**
     * Scope to filter only active customers.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', CustomerStatus::Active);
    }

    /**
     * Scope to filter customers by status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param CustomerStatus $status The status to filter by
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, CustomerStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to search customers by name, email, or company.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $search The search term
     * @return \Illuminate\Database\Eloquent\Builder
     */
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

    /**
     * Check if the customer is active.
     *
     * @return bool True if the customer has active status
     */
    public function isActive(): bool
    {
        return $this->status === CustomerStatus::Active;
    }
}
