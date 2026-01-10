<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;

/**
 * Value Object representing an email address.
 *
 * Provides validation and utility methods for working with
 * email addresses in a type-safe manner.
 *
 * @property-read string $value The validated email address
 */
readonly class Email
{
    /**
     * Create a new Email value object.
     *
     * @param string $value The email address
     * @throws InvalidArgumentException When email format is invalid
     */
    public function __construct(
        public string $value
    ) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address: {$value}");
        }
    }

    /**
     * Convert the email to a string.
     *
     * @return string The email address
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Check if this email equals another email.
     *
     * @param Email $other The email to compare with
     * @return bool True if emails are equal
     */
    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get the domain part of the email address.
     *
     * @return string The domain (everything after @)
     */
    public function domain(): string
    {
        return explode('@', $this->value)[1];
    }

    /**
     * Get the local part of the email address.
     *
     * @return string The local part (everything before @)
     */
    public function localPart(): string
    {
        return explode('@', $this->value)[0];
    }
}
