<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Base interface for all repository implementations.
 *
 * Defines common CRUD operations and query methods that all
 * repositories must implement.
 */
interface BaseRepositoryInterface
{
    /**
     * Get all models.
     *
     * @return Collection All model instances
     */
    public function all(): Collection;

    /**
     * Find a model by ID.
     *
     * @param int $id The model ID
     * @return Model|null The model or null if not found
     */
    public function find(int $id): ?Model;

    /**
     * Find a model by ID or throw an exception.
     *
     * @param int $id The model ID
     * @return Model The found model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Model;

    /**
     * Find a model by UUID.
     *
     * @param string $uuid The model UUID
     * @return Model|null The model or null if not found
     */
    public function findByUuid(string $uuid): ?Model;

    /**
     * Create a new model.
     *
     * @param array $data The model data
     * @return Model The created model
     */
    public function create(array $data): Model;

    /**
     * Update an existing model.
     *
     * @param Model $model The model to update
     * @param array $data The update data
     * @return Model The updated model
     */
    public function update(Model $model, array $data): Model;

    /**
     * Soft delete a model.
     *
     * @param Model $model The model to delete
     * @return bool True if deleted successfully
     */
    public function delete(Model $model): bool;

    /**
     * Permanently delete a model.
     *
     * @param Model $model The model to force delete
     * @return bool True if deleted successfully
     */
    public function forceDelete(Model $model): bool;

    /**
     * Restore a soft-deleted model.
     *
     * @param Model $model The model to restore
     * @return bool True if restored successfully
     */
    public function restore(Model $model): bool;

    /**
     * Get a paginated list of models.
     *
     * @param int $perPage Number of items per page
     * @param array $filters Filter criteria
     * @return LengthAwarePaginator Paginated results
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Find models matching conditions.
     *
     * @param array $conditions Key-value pairs of conditions
     * @return Collection Matching models
     */
    public function findWhere(array $conditions): Collection;

    /**
     * Find the first model matching conditions.
     *
     * @param array $conditions Key-value pairs of conditions
     * @return Model|null The first matching model or null
     */
    public function findWhereFirst(array $conditions): ?Model;

    /**
     * Count models matching conditions.
     *
     * @param array $conditions Key-value pairs of conditions
     * @return int The count of matching models
     */
    public function count(array $conditions = []): int;

    /**
     * Check if any model matches conditions.
     *
     * @param array $conditions Key-value pairs of conditions
     * @return bool True if any model matches
     */
    public function exists(array $conditions): bool;
}
