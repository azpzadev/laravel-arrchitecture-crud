<?php

declare(strict_types=1);

namespace App\Domain\Auth\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

/**
 * Exception thrown when a user cannot be found.
 *
 * This exception is thrown when attempting to look up a user
 * by username or other identifier that does not exist.
 */
class UserNotFoundException extends DomainException
{
    /**
     * Create a new UserNotFoundException instance.
     *
     * @param string $username The username that was not found
     */
    public function __construct(string $username)
    {
        parent::__construct(
            message: "User not found with username: {$username}",
            errorCode: 'USER_NOT_FOUND',
            code: 404
        );
    }
}
