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

/**
 * Service class for customer domain operations.
 *
 * Orchestrates customer-related actions and provides a unified
 * interface for customer CRUD operations.
 */
class CustomerService
{
    /**
     * Create a new CustomerService instance.
     *
     * @param CustomerRepositoryInterface $repository The customer repository
     * @param CreateCustomerAction $createAction Action for creating customers
     * @param UpdateCustomerAction $updateAction Action for updating customers
     * @param DeleteCustomerAction $deleteAction Action for deleting customers
     * @param RestoreCustomerAction $restoreAction Action for restoring customers
     */
    public function __construct(
        private CustomerRepositoryInterface $repository,
        private CreateCustomerAction $createAction,
        private UpdateCustomerAction $updateAction,
        private DeleteCustomerAction $deleteAction,
        private RestoreCustomerAction $restoreAction,
    ) {}

    /**
     * Get a paginated list of customers with optional filters.
     *
     * @param CustomerFilterData $filters The filter criteria
     * @return LengthAwarePaginator Paginated customer results
     */
    public function paginate(CustomerFilterData $filters): LengthAwarePaginator
    {
        return $this->repository->paginate(
            perPage: $filters->pagination?->perPage ?? 15,
            filters: $filters->toArray()
        );
    }

    /**
     * Find a customer by ID.
     *
     * @param int $id The customer ID
     * @return Customer The found customer
     * @throws CustomerNotFoundException When customer is not found
     */
    public function find(int $id): Customer
    {
        $customer = $this->repository->find($id);

        if (!$customer) {
            throw new CustomerNotFoundException($id);
        }

        return $customer;
    }

    /**
     * Find a customer by UUID.
     *
     * @param string $uuid The customer UUID
     * @return Customer The found customer
     * @throws CustomerNotFoundException When customer is not found
     */
    public function findByUuid(string $uuid): Customer
    {
        $customer = $this->repository->findByUuid($uuid);

        if (!$customer) {
            throw new CustomerNotFoundException($uuid);
        }

        return $customer;
    }

    /**
     * Find a customer by email address.
     *
     * @param string $email The customer email
     * @return Customer|null The found customer or null
     */
    public function findByEmail(string $email): ?Customer
    {
        return $this->repository->findByEmail($email);
    }

    /**
     * Create a new customer.
     *
     * @param CustomerData $data The customer data
     * @return Customer The created customer
     */
    public function create(CustomerData $data): Customer
    {
        return $this->createAction->execute($data);
    }

    /**
     * Update an existing customer.
     *
     * @param Customer $customer The customer to update
     * @param CustomerData $data The new customer data
     * @return Customer The updated customer
     */
    public function update(Customer $customer, CustomerData $data): Customer
    {
        return $this->updateAction->execute($customer, $data);
    }

    /**
     * Delete a customer.
     *
     * @param Customer $customer The customer to delete
     * @param bool $force Whether to permanently delete
     * @return bool True if deletion was successful
     */
    public function delete(Customer $customer, bool $force = false): bool
    {
        return $this->deleteAction->execute($customer, $force);
    }

    /**
     * Restore a soft-deleted customer.
     *
     * @param Customer $customer The customer to restore
     * @return bool True if restoration was successful
     */
    public function restore(Customer $customer): bool
    {
        return $this->restoreAction->execute($customer);
    }
}
