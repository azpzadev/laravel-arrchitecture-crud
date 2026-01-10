<?php

declare(strict_types=1);

namespace App\Domain\Customer\DTOs;

use App\Domain\Customer\Enums\CustomerStatus;

/**
 * Data Transfer Object for customer information.
 *
 * Encapsulates all customer data for creating or updating
 * customer records in the system.
 *
 * @property-read string $name The customer's full name
 * @property-read string $email The customer's email address
 * @property-read string|null $phone The customer's phone number
 * @property-read string|null $address The customer's physical address
 * @property-read string|null $company The customer's company name
 * @property-read CustomerStatus $status The customer's account status
 * @property-read array $metadata Additional custom data for the customer
 */
readonly class CustomerData
{
    /**
     * Create a new CustomerData instance.
     *
     * @param string $name The customer's full name
     * @param string $email The customer's email address
     * @param string|null $phone The customer's phone number
     * @param string|null $address The customer's physical address
     * @param string|null $company The customer's company name
     * @param CustomerStatus $status The customer's account status
     * @param array $metadata Additional custom data
     */
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $company = null,
        public CustomerStatus $status = CustomerStatus::Active,
        public array $metadata = [],
    ) {}

    /**
     * Create a CustomerData instance from an array.
     *
     * @param array{name: string, email: string, phone?: string|null, address?: string|null, company?: string|null, status?: string|CustomerStatus, metadata?: array} $data The customer data array
     * @return self A new CustomerData instance
     */
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

    /**
     * Convert the DTO to an array.
     *
     * @return array{name: string, email: string, phone: string|null, address: string|null, company: string|null, status: string, metadata: array} The customer data as an array
     */
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
