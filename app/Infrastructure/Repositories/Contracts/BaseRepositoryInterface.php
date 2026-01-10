<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Model;

    public function findOrFail(int $id): Model;

    public function findByUuid(string $uuid): ?Model;

    public function create(array $data): Model;

    public function update(Model $model, array $data): Model;

    public function delete(Model $model): bool;

    public function forceDelete(Model $model): bool;

    public function restore(Model $model): bool;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function findWhere(array $conditions): Collection;

    public function findWhereFirst(array $conditions): ?Model;

    public function count(array $conditions = []): int;

    public function exists(array $conditions): bool;
}
