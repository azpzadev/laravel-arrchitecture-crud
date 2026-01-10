<?php

declare(strict_types=1);

namespace App\Domain\Customer\Actions;

use App\Domain\Customer\DTOs\CustomerData;
use App\Domain\Customer\Events\CustomerUpdated;
use App\Domain\Customer\Exceptions\CustomerAlreadyExistsException;
use App\Infrastructure\Models\Customer;
use App\Infrastructure\Repositories\Contracts\CustomerRepositoryInterface;

/**
 * Action class for updating an existing customer.
 *
 * Handles the business logic for customer updates including
 * email uniqueness validation, change tracking, and event dispatching.
 */
class UpdateCustomerAction
{
    /**
     * Create a new UpdateCustomerAction instance.
     *
     * @param CustomerRepositoryInterface $repository The customer repository
     */
    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {}

    /**
     * Execute the customer update action.
     *
     * @param Customer $customer The customer to update
     * @param CustomerData $data The new customer data
     * @return Customer The updated customer
     * @throws CustomerAlreadyExistsException When new email already exists
     */
    public function execute(Customer $customer, CustomerData $data): Customer
    {
        // Check if email is being changed and if it already exists
        if ($data->email !== $customer->email && $this->repository->existsByEmail($data->email)) {
            throw new CustomerAlreadyExistsException($data->email);
        }

        $originalAttributes = $customer->getAttributes();

        $customer = $this->repository->update($customer, $data->toArray());

        $changedAttributes = array_diff_assoc($customer->getAttributes(), $originalAttributes);

        event(new CustomerUpdated($customer, $changedAttributes));

        return $customer;
    }
}
