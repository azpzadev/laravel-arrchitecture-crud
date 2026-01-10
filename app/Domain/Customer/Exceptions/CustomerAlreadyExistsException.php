<?php

declare(strict_types=1);

namespace App\Domain\Customer\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

/**
 * Exception thrown when attempting to create a duplicate customer.
 *
 * This exception is thrown when trying to create a customer with
 * an email address that already exists in the system.
 */
class CustomerAlreadyExistsException extends DomainException
{
    /**
     * Create a new CustomerAlreadyExistsException instance.
     *
     * @param string $email The duplicate email address
     */
    public function __construct(string $email)
    {
        parent::__construct(
            message: "Customer with email {$email} already exists",
            errorCode: 'CUSTOMER_ALREADY_EXISTS',
            code: 409
        );
    }
}
