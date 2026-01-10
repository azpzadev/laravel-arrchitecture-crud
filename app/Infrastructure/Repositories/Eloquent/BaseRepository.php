<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Eloquent;

use App\Infrastructure\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Abstract base repository with common Eloquent operations.
 *
 * Provides default implementations for CRUD operations and
 * query methods that can be extended by specific repositories.
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * The Eloquent model instance.
     *
     * @var Model
     */
    protected Model $model;

    /**
     * Create a new repository instance.
     */
    public function __construct()
    {
        $this->model = $this->resolveModel();
    }

    /**
     * Resolve and return the Eloquent model instance.
     *
     * @return Model The model instance for this repository
     */
    abstract protected function resolveModel(): Model;

    /**
     * {@inheritdoc}
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * {@inheritdoc}
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrFail(int $id): Model
    {
        $model = $this->find($id);

        if (!$model) {
            throw new ModelNotFoundException("Model with ID {$id} not found.");
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function findByUuid(string $uuid): ?Model
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model->fresh();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function forceDelete(Model $model): bool
    {
        return $model->forceDelete();
    }

    /**
     * {@inheritdoc}
     */
    public function restore(Model $model): bool
    {
        return $model->restore();
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        $query = $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function findWhere(array $conditions): Collection
    {
        return $this->model->where($conditions)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findWhereFirst(array $conditions): ?Model
    {
        return $this->model->where($conditions)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function count(array $conditions = []): int
    {
        $query = $this->model->newQuery();

        if (!empty($conditions)) {
            $query->where($conditions);
        }

        return $query->count();
    }

    /**
     * {@inheritdoc}
     */
    public function exists(array $conditions): bool
    {
        return $this->model->where($conditions)->exists();
    }

    /**
     * Apply filters to the query builder.
     *
     * Override this method in child classes to implement
     * specific filtering logic.
     *
     * @param Builder $query The query builder
     * @param array $filters The filters to apply
     * @return Builder The modified query builder
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query;
    }

    /**
     * Get a new query builder instance for the model.
     *
     * @return Builder A fresh query builder
     */
    protected function newQuery(): Builder
    {
        return $this->model->newQuery();
    }
}
