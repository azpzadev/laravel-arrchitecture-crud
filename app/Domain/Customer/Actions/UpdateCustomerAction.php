<?php

declare(strict_types=1);

namespace App\Domain\Customer\Actions;

use App\Domain\Customer\DTOs\CustomerData;
use App\Domain\Customer\Events\CustomerUpdated;
use App\Domain\Customer\Exceptions\CustomerAlreadyExistsException;
use App\Infrastructure\Models\Customer;
use App\Infrastructure\Repositories\Contracts\CustomerRepositoryInterface;

class UpdateCustomerAction
{
    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {}

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
