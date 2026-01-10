<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Contracts;

use App\Infrastructure\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email): ?Customer;

    public function existsByEmail(string $email): bool;

    public function findByStatus(string $status): array;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function getActiveCustomers(): array;

    public function searchCustomers(string $query): array;
}
