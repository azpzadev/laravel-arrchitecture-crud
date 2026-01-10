<?php

declare(strict_types=1);

namespace App\Domain\Customer\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class CustomerAlreadyExistsException extends DomainException
{
    public function __construct(string $email)
    {
        parent::__construct(
            message: "Customer with email {$email} already exists",
            errorCode: 'CUSTOMER_ALREADY_EXISTS',
            code: 409
        );
    }
}
