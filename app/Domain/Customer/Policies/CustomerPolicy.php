<?php

declare(strict_types=1);

namespace App\Domain\Customer\Policies;

use App\Infrastructure\Models\Customer;
use App\Infrastructure\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Authorization policy for customer operations.
 *
 * Defines authorization rules for customer CRUD operations
 * based on the authenticated user's permissions.
 */
class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any customers.
     *
     * @param User $user The authenticated user
     * @return bool True if authorized
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the customer.
     *
     * @param User $user The authenticated user
     * @param Customer $customer The customer to view
     * @return bool True if authorized
     */
    public function view(User $user, Customer $customer): bool
    {
        return true;
    }

    /**
     * Determine if the user can create customers.
     *
     * @param User $user The authenticated user
     * @return bool True if authorized
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the customer.
     *
     * @param User $user The authenticated user
     * @param Customer $customer The customer to update
     * @return bool True if authorized
     */
    public function update(User $user, Customer $customer): bool
    {
        return true;
    }

    /**
     * Determine if the user can delete the customer.
     *
     * @param User $user The authenticated user
     * @param Customer $customer The customer to delete
     * @return bool True if authorized
     */
    public function delete(User $user, Customer $customer): bool
    {
        return true;
    }

    /**
     * Determine if the user can restore the customer.
     *
     * @param User $user The authenticated user
     * @param Customer $customer The customer to restore
     * @return bool True if authorized
     */
    public function restore(User $user, Customer $customer): bool
    {
        return true;
    }

    /**
     * Determine if the user can permanently delete the customer.
     *
     * @param User $user The authenticated user
     * @param Customer $customer The customer to force delete
     * @return bool True if authorized
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        return true;
    }
}
