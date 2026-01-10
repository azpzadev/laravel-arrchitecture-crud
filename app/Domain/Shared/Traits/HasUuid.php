<?php

declare(strict_types=1);

namespace App\Domain\Shared\Traits;

use Illuminate\Support\Str;

/**
 * Trait for adding UUID support to Eloquent models.
 *
 * Provides automatic UUID generation on model creation and
 * convenient methods for finding models by UUID.
 */
trait HasUuid
{
    /**
     * Boot the HasUuid trait.
     *
     * Automatically generates a UUID when creating a new model
     * if one is not already set.
     *
     * @return void
     */
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    /**
     * Get the route key name for Laravel route model binding.
     *
     * @return string The column name used for route binding
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Find a model by its UUID.
     *
     * @param string $uuid The UUID to search for
     * @return static|null The model or null if not found
     */
    public static function findByUuid(string $uuid): ?static
    {
        return static::where('uuid', $uuid)->first();
    }

    /**
     * Find a model by its UUID or throw an exception.
     *
     * @param string $uuid The UUID to search for
     * @return static The found model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException When model is not found
     */
    public static function findByUuidOrFail(string $uuid): static
    {
        return static::where('uuid', $uuid)->firstOrFail();
    }
}
