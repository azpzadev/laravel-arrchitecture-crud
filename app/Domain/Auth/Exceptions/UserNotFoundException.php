<?php

declare(strict_types=1);

namespace App\Domain\Auth\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class UserNotFoundException extends DomainException
{
    public function __construct(string $username)
    {
        parent::__construct(
            message: "User not found with username: {$username}",
            errorCode: 'USER_NOT_FOUND',
            code: 404
        );
    }
}
