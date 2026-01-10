<?php

declare(strict_types=1);

namespace App\Domain\Customer\Services;

use App\Domain\Customer\Actions\CreateCustomerAction;
use App\Domain\Customer\Actions\DeleteCustomerAction;
use App\Domain\Customer\Actions\RestoreCustomerAction;
use App\Domain\Customer\Actions\UpdateCustomerAction;
use App\Domain\Customer\DTOs\CustomerData;
use App\Domain\Customer\DTOs\CustomerFilterData;
use App\Domain\Customer\Exceptions\CustomerNotFoundException;
use App\Infrastructure\Models\Customer;
use App\Infrastructure\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function __construct(
        private CustomerRepositoryInterface $repository,
        private CreateCustomerAction $createAction,
        private UpdateCustomerAction $updateAction,
        private DeleteCustomerAction $deleteAction,
        private RestoreCustomerAction $restoreAction,
    ) {}

    public function paginate(CustomerFilterData $filters): LengthAwarePaginator
    {
        return $this->repository->paginate(
            perPage: $filters->pagination?->perPage ?? 15,
            filters: $filters->toArray()
        );
    }

    public function find(int $id): Customer
    {
        $customer = $this->repository->find($id);

        if (!$customer) {
            throw new CustomerNotFoundException($id);
        }

        return $customer;
    }

    public function findByUuid(string $uuid): Customer
    {
        $customer = $this->repository->findByUuid($uuid);

        if (!$customer) {
            throw new CustomerNotFoundException($uuid);
        }

        return $customer;
    }

    public function findByEmail(string $email): ?Customer
    {
        return $this->repository->findByEmail($email);
    }

    public function create(CustomerData $data): Customer
    {
        return $this->createAction->execute($data);
    }

    public function update(Customer $customer, CustomerData $data): Customer
    {
        return $this->updateAction->execute($customer, $data);
    }

    public function delete(Customer $customer, bool $force = false): bool
    {
        return $this->deleteAction->execute($customer, $force);
    }

    public function restore(Customer $customer): bool
    {
        return $this->restoreAction->execute($customer);
    }
}
