<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exceptions;

use Exception;

/**
 * Base exception class for domain layer errors.
 *
 * Provides a consistent exception structure with error codes
 * for all domain-specific exceptions.
 */
class DomainException extends Exception
{
    /**
     * The domain-specific error code.
     *
     * @var string
     */
    protected string $errorCode;

    /**
     * Create a new DomainException instance.
     *
     * @param string $message The exception message
     * @param string $errorCode The domain-specific error code
     * @param int $code The HTTP status code
     * @param Exception|null $previous The previous exception for chaining
     */
    public function __construct(
        string $message,
        string $errorCode = 'DOMAIN_ERROR',
        int $code = 400,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
    }

    /**
     * Get the domain-specific error code.
     *
     * @return string The error code
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Convert the exception to an array for API responses.
     *
     * @return array{error_code: string, message: string} The exception data
     */
    public function toArray(): array
    {
        return [
            'error_code' => $this->errorCode,
            'message' => $this->getMessage(),
        ];
    }
}
