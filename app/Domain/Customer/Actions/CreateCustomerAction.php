<?php

declare(strict_types=1);

namespace App\Domain\Customer\Actions;

use App\Domain\Customer\DTOs\CustomerData;
use App\Domain\Customer\Events\CustomerCreated;
use App\Domain\Customer\Exceptions\CustomerAlreadyExistsException;
use App\Infrastructure\Models\Customer;
use App\Infrastructure\Repositories\Contracts\CustomerRepositoryInterface;

class CreateCustomerAction
{
    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {}

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
