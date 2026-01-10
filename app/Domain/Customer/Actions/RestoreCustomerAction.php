<?php

declare(strict_types=1);

namespace App\Domain\Customer\Actions;

use App\Infrastructure\Models\Customer;
use App\Infrastructure\Repositories\Contracts\CustomerRepositoryInterface;

class RestoreCustomerAction
{
    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {}

    public function execute(Customer $customer): bool
    {
        return $this->repository->restore($customer);
    }
}
