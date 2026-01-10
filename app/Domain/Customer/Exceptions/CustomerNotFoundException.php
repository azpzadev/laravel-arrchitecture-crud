<?php

declare(strict_types=1);

namespace App\Domain\Customer\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

/**
 * Exception thrown when a customer cannot be found.
 *
 * This exception is thrown when attempting to look up a customer
 * by ID, UUID, or other identifier that does not exist.
 */
class CustomerNotFoundException extends DomainException
{
    /**
     * Create a new CustomerNotFoundException instance.
     *
     * @param int|string $identifier The customer ID or UUID that was not found
     */
    public function __construct(int|string $identifier)
    {
        parent::__construct(
            message: "Customer not found with identifier: {$identifier}",
            errorCode: 'CUSTOMER_NOT_FOUND',
            code: 404
        );
    }
}
