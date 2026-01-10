<?php

declare(strict_types=1);

namespace App\Domain\Auth\DTOs;

/**
 * Data Transfer Object for authentication token response.
 *
 * Contains the access token and related metadata returned
 * after successful authentication.
 *
 * @property-read string $accessToken The Bearer access token
 * @property-read string $tokenType The token type (typically 'Bearer')
 * @property-read string|null $expiresAt Token expiration timestamp (ISO 8601)
 */
readonly class AuthTokenData
{
    /**
     * Create a new AuthTokenData instance.
     *
     * @param string $accessToken The access token string
     * @param string $tokenType The token type (default: 'Bearer')
     * @param string|null $expiresAt Optional expiration timestamp
     */
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public ?string $expiresAt = null,
    ) {}

    /**
     * Create an AuthTokenData instance from an array.
     *
     * @param array{access_token: string, token_type?: string, expires_at?: string|null} $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            accessToken: $data['access_token'],
            tokenType: $data['token_type'] ?? 'Bearer',
            expiresAt: $data['expires_at'] ?? null,
        );
    }

    /**
     * Convert the DTO to an array representation.
     *
     * @return array{access_token: string, token_type: string, expires_at?: string|null}
     */
    public function toArray(): array
    {
        return array_filter([
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
            'expires_at' => $this->expiresAt,
        ], fn($value) => $value !== null);
    }
}
