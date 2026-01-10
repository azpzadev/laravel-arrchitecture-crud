<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exceptions;

use Exception;

class DomainException extends Exception
{
    protected string $errorCode;

    public function __construct(
        string $message,
        string $errorCode = 'DOMAIN_ERROR',
        int $code = 400,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function toArray(): array
    {
        return [
            'error_code' => $this->errorCode,
            'message' => $this->getMessage(),
        ];
    }
}
