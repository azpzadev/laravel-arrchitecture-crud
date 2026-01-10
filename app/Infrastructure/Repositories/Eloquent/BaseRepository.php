<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Eloquent;

use App\Infrastructure\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct()
    {
        $this->model = $this->resolveModel();
    }

    abstract protected function resolveModel(): Model;

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id): Model
    {
        $model = $this->find($id);

        if (!$model) {
            throw new ModelNotFoundException("Model with ID {$id} not found.");
        }

        return $model;
    }

    public function findByUuid(string $uuid): ?Model
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model->fresh();
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function forceDelete(Model $model): bool
    {
        return $model->forceDelete();
    }

    public function restore(Model $model): bool
    {
        return $model->restore();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        $query = $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    public function findWhere(array $conditions): Collection
    {
        return $this->model->where($conditions)->get();
    }

    public function findWhereFirst(array $conditions): ?Model
    {
        return $this->model->where($conditions)->first();
    }

    public function count(array $conditions = []): int
    {
        $query = $this->model->newQuery();

        if (!empty($conditions)) {
            $query->where($conditions);
        }

        return $query->count();
    }

    public function exists(array $conditions): bool
    {
        return $this->model->where($conditions)->exists();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query;
    }

    protected function newQuery(): Builder
    {
        return $this->model->newQuery();
    }
}
