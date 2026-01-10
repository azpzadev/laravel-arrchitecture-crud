<?php

declare(strict_types=1);

namespace App\Domain\Auth\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

/**
 * Exception thrown when login credentials are invalid.
 *
 * This exception is thrown during authentication when the
 * provided username or password does not match any user record.
 */
class InvalidCredentialsException extends DomainException
{
    /**
     * Create a new InvalidCredentialsException instance.
     */
    public function __construct()
    {
        parent::__construct(
            message: 'The provided credentials are incorrect.',
            errorCode: 'INVALID_CREDENTIALS',
            code: 401
        );
    }
}
