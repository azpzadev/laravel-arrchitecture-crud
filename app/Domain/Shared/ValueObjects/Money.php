<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;

/**
 * Value Object representing a monetary amount with currency.
 *
 * Stores amounts as integers (cents) to avoid floating-point
 * precision issues. Provides arithmetic and comparison operations.
 *
 * @property-read int $amount The amount in smallest currency unit (cents)
 * @property-read string $currency The ISO 4217 currency code
 */
readonly class Money
{
    /**
     * Create a new Money value object.
     *
     * @param int $amount The amount in cents (smallest currency unit)
     * @param string $currency The ISO 4217 currency code
     * @throws InvalidArgumentException When amount is negative
     */
    public function __construct(
        public int $amount,
        public string $currency = 'USD'
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount cannot be negative');
        }
    }

    /**
     * Create a Money instance from a decimal amount.
     *
     * @param float $amount The decimal amount (e.g., 10.50)
     * @param string $currency The ISO 4217 currency code
     * @return self A new Money instance
     */
    public static function fromDecimal(float $amount, string $currency = 'USD'): self
    {
        return new self((int) round($amount * 100), $currency);
    }

    /**
     * Convert the amount to a decimal value.
     *
     * @return float The amount as a decimal (e.g., 10.50)
     */
    public function toDecimal(): float
    {
        return $this->amount / 100;
    }

    /**
     * Format the money as a human-readable string.
     *
     * @return string Formatted amount with currency (e.g., "10.50 USD")
     */
    public function format(): string
    {
        return number_format($this->toDecimal(), 2) . ' ' . $this->currency;
    }

    /**
     * Add another Money amount to this one.
     *
     * @param Money $other The amount to add
     * @return self A new Money instance with the sum
     * @throws InvalidArgumentException When currencies don't match
     */
    public function add(Money $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amount + $other->amount, $this->currency);
    }

    /**
     * Subtract another Money amount from this one.
     *
     * @param Money $other The amount to subtract
     * @return self A new Money instance with the difference
     * @throws InvalidArgumentException When currencies don't match or result is negative
     */
    public function subtract(Money $other): self
    {
        $this->assertSameCurrency($other);

        if ($this->amount < $other->amount) {
            throw new InvalidArgumentException('Cannot subtract: result would be negative');
        }

        return new self($this->amount - $other->amount, $this->currency);
    }

    /**
     * Multiply the amount by a factor.
     *
     * @param float $multiplier The multiplication factor
     * @return self A new Money instance with the product
     */
    public function multiply(float $multiplier): self
    {
        return new self((int) round($this->amount * $multiplier), $this->currency);
    }

    /**
     * Check if this Money equals another Money.
     *
     * @param Money $other The Money to compare with
     * @return bool True if amount and currency are equal
     */
    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    /**
     * Check if this Money is greater than another.
     *
     * @param Money $other The Money to compare with
     * @return bool True if this amount is greater
     * @throws InvalidArgumentException When currencies don't match
     */
    public function isGreaterThan(Money $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount > $other->amount;
    }

    /**
     * Check if this Money is less than another.
     *
     * @param Money $other The Money to compare with
     * @return bool True if this amount is less
     * @throws InvalidArgumentException When currencies don't match
     */
    public function isLessThan(Money $other): bool
    {
        $this->assertSameCurrency($other);

        return $this->amount < $other->amount;
    }

    /**
     * Assert that two Money objects have the same currency.
     *
     * @param Money $other The Money to compare currency with
     * @return void
     * @throws InvalidArgumentException When currencies don't match
     */
    private function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Cannot operate on different currencies');
        }
    }
}
