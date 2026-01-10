<?php

declare(strict_types=1);

namespace App\Domain\Shared\DTOs;

readonly class FilterData
{
    public function __construct(
        public ?string $search = null,
        public ?string $status = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?PaginationData $pagination = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            status: $data['status'] ?? null,
            startDate: $data['start_date'] ?? null,
            endDate: $data['end_date'] ?? null,
            pagination: PaginationData::fromArray($data),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'status' => $this->status,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            ...$this->pagination?->toArray() ?? [],
        ], fn($value) => $value !== null);
    }
}
