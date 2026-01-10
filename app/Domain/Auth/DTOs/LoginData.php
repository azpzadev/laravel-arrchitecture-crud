<?php

declare(strict_types=1);

namespace App\Domain\Auth\DTOs;

/**
 * Data Transfer Object for user login credentials.
 *
 * Encapsulates the login request data including username, password,
 * and optional device name for token identification.
 *
 * @property-read string $username The user's username for authentication
 * @property-read string $password The user's password (plain text for validation)
 * @property-read string|null $deviceName The device name for token identification
 */
readonly class LoginData
{
    /**
     * Create a new LoginData instance.
     *
     * @param string $username The user's username
     * @param string $password The user's password
     * @param string|null $deviceName The device name (defaults to 'api')
     */
    public function __construct(
        public string $username,
        public string $password,
        public ?string $deviceName = null,
    ) {}

    /**
     * Create a LoginData instance from an array.
     *
     * @param array{username: string, password: string, device_name?: string|null} $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            username: $data['username'],
            password: $data['password'],
            deviceName: $data['device_name'] ?? 'api',
        );
    }

    /**
     * Convert the DTO to an array representation.
     *
     * @return array{username: string, password: string, device_name: string|null}
     */
    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'device_name' => $this->deviceName,
        ];
    }
}
