<?php

declare(strict_types=1);

namespace App\Domain\Auth\DTOs;

readonly class AuthTokenData
{
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public ?string $expiresAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            accessToken: $data['access_token'],
            tokenType: $data['token_type'] ?? 'Bearer',
            expiresAt: $data['expires_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
            'expires_at' => $this->expiresAt,
        ], fn($value) => $value !== null);
    }
}
