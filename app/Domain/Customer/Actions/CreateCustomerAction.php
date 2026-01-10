<?php

declare(strict_types=1);

namespace App\Domain\Customer\Actions;

use App\Domain\Customer\DTOs\CustomerData;
use App\Domain\Customer\Events\CustomerCreated;
use App\Domain\Customer\Exceptions\CustomerAlreadyExistsException;
use App\Infrastructure\Models\Customer;
use App\Infrastructure\Repositories\Contracts\CustomerRepositoryInterface;

/**
 * Action class for creating a new customer.
 *
 * Handles the business logic for customer creation including
 * duplicate email validation and event dispatching.
 */
class CreateCustomerAction
{
    /**
     * Create a new CreateCustomerAction instance.
     *
     * @param CustomerRepositoryInterface $repository The customer repository
     */
    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {}

    /**
     * Execute the customer creation action.
     *
     * @param CustomerData $data The customer data to create
     * @return Customer The newly created customer
     * @throws CustomerAlreadyExistsException When email already exists
     */
    public function execute(CustomerData $data): Customer
    {
        if ($this->repository->existsByEmail($data->email)) {
            throw new CustomerAlreadyExistsException($data->email);
        }

        $customer = $this->repository->create($data->toArray());

        event(new CustomerCreated($customer));

        return $customer;
    }
}
