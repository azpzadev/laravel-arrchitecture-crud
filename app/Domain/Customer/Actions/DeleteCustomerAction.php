<?php

declare(strict_types=1);

namespace App\Domain\Customer\Actions;

use App\Domain\Customer\Events\CustomerDeleted;
use App\Infrastructure\Models\Customer;
use App\Infrastructure\Repositories\Contracts\CustomerRepositoryInterface;

/**
 * Action class for deleting a customer.
 *
 * Handles the business logic for customer deletion including
 * soft-delete and force-delete options with event dispatching.
 */
class DeleteCustomerAction
{
    /**
     * Create a new DeleteCustomerAction instance.
     *
     * @param CustomerRepositoryInterface $repository The customer repository
     */
    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {}

    /**
     * Execute the customer deletion action.
     *
     * @param Customer $customer The customer to delete
     * @param bool $force Whether to permanently delete (true) or soft-delete (false)
     * @return bool True if deletion was successful
     */
    public function execute(Customer $customer, bool $force = false): bool
    {
        if ($force) {
            $result = $this->repository->forceDelete($customer);
        } else {
            $result = $this->repository->delete($customer);
        }

        event(new CustomerDeleted($customer, $force));

        return $result;
    }
}
