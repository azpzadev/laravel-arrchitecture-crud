<?php

declare(strict_types=1);

namespace App\Domain\Customer\Actions;

use App\Domain\Customer\Events\CustomerDeleted;
use App\Infrastructure\Models\Customer;
use App\Infrastructure\Repositories\Contracts\CustomerRepositoryInterface;

class DeleteCustomerAction
{
    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {}

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
