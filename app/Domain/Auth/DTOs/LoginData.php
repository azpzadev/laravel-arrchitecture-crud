<?php

declare(strict_types=1);

namespace App\Domain\Auth\DTOs;

readonly class LoginData
{
    public function __construct(
        public string $username,
        public string $password,
        public ?string $deviceName = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            username: $data['username'],
            password: $data['password'],
            deviceName: $data['device_name'] ?? 'api',
        );
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'device_name' => $this->deviceName,
        ];
    }
}
