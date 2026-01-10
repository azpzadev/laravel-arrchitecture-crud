<?php

declare(strict_types=1);

namespace App\Domain\Customer\DTOs;

use App\Domain\Customer\Enums\CustomerStatus;
use App\Domain\Shared\DTOs\PaginationData;

readonly class CustomerFilterData
{
    public function __construct(
        public ?string $search = null,
        public ?CustomerStatus $status = null,
        public ?string $company = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?bool $withTrashed = false,
        public ?PaginationData $pagination = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            status: isset($data['status']) ? CustomerStatus::tryFrom($data['status']) : null,
            company: $data['company'] ?? null,
            startDate: $data['start_date'] ?? null,
            endDate: $data['end_date'] ?? null,
            withTrashed: filter_var($data['with_trashed'] ?? false, FILTER_VALIDATE_BOOLEAN),
            pagination: PaginationData::fromArray($data),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'status' => $this->status?->value,
            'company' => $this->company,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'with_trashed' => $this->withTrashed,
            ...$this->pagination?->toArray() ?? [],
        ], fn($value) => $value !== null);
    }
}
