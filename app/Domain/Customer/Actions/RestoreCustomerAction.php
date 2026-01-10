<?php

declare(strict_types=1);

namespace App\Domain\Customer\Actions;

use App\Infrastructure\Models\Customer;
use App\Infrastructure\Repositories\Contracts\CustomerRepositoryInterface;

/**
 * Action class for restoring a soft-deleted customer.
 *
 * Handles the business logic for restoring customers
 * that were previously soft-deleted.
 */
class RestoreCustomerAction
{
    /**
     * Create a new RestoreCustomerAction instance.
     *
     * @param CustomerRepositoryInterface $repository The customer repository
     */
    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {}

    /**
     * Execute the customer restore action.
     *
     * @param Customer $customer The soft-deleted customer to restore
     * @return bool True if restoration was successful
     */
    public function execute(Customer $customer): bool
    {
        return $this->repository->restore($customer);
    }
}
