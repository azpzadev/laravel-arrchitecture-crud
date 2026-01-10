<?php

declare(strict_types=1);

namespace App\Domain\Customer\DTOs;

use App\Domain\Customer\Enums\CustomerStatus;

readonly class CustomerData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $company = null,
        public CustomerStatus $status = CustomerStatus::Active,
        public array $metadata = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            company: $data['company'] ?? null,
            status: isset($data['status'])
                ? (is_string($data['status']) ? CustomerStatus::from($data['status']) : $data['status'])
                : CustomerStatus::Active,
            metadata: $data['metadata'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'company' => $this->company,
            'status' => $this->status->value,
            'metadata' => $this->metadata,
        ];
    }
}
