<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Eloquent;

use App\Domain\Customer\Enums\CustomerStatus;
use App\Infrastructure\Models\Customer;
use App\Infrastructure\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent implementation of CustomerRepositoryInterface.
 *
 * Provides data access methods for Customer entities using
 * Eloquent ORM with filtering and search capabilities.
 */
class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    protected function resolveModel(): Model
    {
        return new Customer();
    }

    /**
     * {@inheritdoc}
     */
    public function findByEmail(string $email): ?Customer
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function existsByEmail(string $email): bool
    {
        return $this->model->where('email', $email)->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function findByStatus(string $status): array
    {
        return $this->model
            ->where('status', $status)
            ->get()
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveCustomers(): array
    {
        return $this->model
            ->active()
            ->orderBy('name')
            ->get()
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function searchCustomers(string $query): array
    {
        return $this->model
            ->search($query)
            ->limit(50)
            ->get()
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->newQuery();

        $query = $this->applyFilters($query, $filters);

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        return $query
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
    }

    /**
     * Apply customer-specific filters to the query.
     *
     * @param Builder $query The query builder
     * @param array $filters Filter criteria (search, status, company, dates, with_trashed)
     * @return Builder The modified query builder
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $status = is_string($filters['status'])
                ? CustomerStatus::tryFrom($filters['status'])
                : $filters['status'];

            if ($status) {
                $query->byStatus($status);
            }
        }

        if (!empty($filters['company'])) {
            $query->where('company', 'ilike', "%{$filters['company']}%");
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        if (!empty($filters['with_trashed'])) {
            $query->withTrashed();
        }

        return $query;
    }
}
