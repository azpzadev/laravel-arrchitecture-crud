<?php

declare(strict_types=1);

namespace App\Domain\Auth\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class InvalidCredentialsException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            message: 'The provided credentials are incorrect.',
            errorCode: 'INVALID_CREDENTIALS',
            code: 401
        );
    }
}
