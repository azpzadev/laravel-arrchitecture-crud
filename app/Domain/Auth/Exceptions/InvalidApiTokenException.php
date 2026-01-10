<?php

declare(strict_types=1);

namespace App\Domain\Auth\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

/**
 * Exception thrown when the API token is invalid or missing.
 *
 * This exception is thrown by the ValidateApiToken middleware
 * when the x-api-token header is missing or contains an invalid token.
 */
class InvalidApiTokenException extends DomainException
{
    /**
     * Create a new InvalidApiTokenException instance.
     */
    public function __construct()
    {
        parent::__construct(
            message: 'Invalid or missing API token.',
            errorCode: 'INVALID_API_TOKEN',
            code: 401
        );
    }
}
