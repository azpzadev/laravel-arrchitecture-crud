<?php

declare(strict_types=1);

namespace App\Domain\Auth\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class InvalidApiTokenException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            message: 'Invalid or missing API token.',
            errorCode: 'INVALID_API_TOKEN',
            code: 401
        );
    }
}
