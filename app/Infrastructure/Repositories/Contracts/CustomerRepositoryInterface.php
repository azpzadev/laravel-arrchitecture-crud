<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Contracts;

use App\Infrastructure\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Interface for customer repository implementations.
 *
 * Extends base repository with customer-specific query methods.
 */
interface CustomerRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find a customer by email address.
     *
     * @param string $email The email address to search
     * @return Customer|null The customer or null if not found
     */
    public function findByEmail(string $email): ?Customer;

    /**
     * Check if a customer exists with the given email.
     *
     * @param string $email The email address to check
     * @return bool True if a customer exists with this email
     */
    public function existsByEmail(string $email): bool;

    /**
     * Find customers by status.
     *
     * @param string $status The status value to filter by
     * @return array Array of matching Customer models
     */
    public function findByStatus(string $status): array;

    /**
     * Get a paginated list of customers with filters.
     *
     * @param int $perPage Number of items per page
     * @param array $filters Filter criteria (search, status, company, dates)
     * @return LengthAwarePaginator Paginated customer results
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Get all active customers.
     *
     * @return array Array of active Customer models
     */
    public function getActiveCustomers(): array;

    /**
     * Search customers by name, email, or company.
     *
     * @param string $query The search query
     * @return array Array of matching Customer models (max 50)
     */
    public function searchCustomers(string $query): array;
}
