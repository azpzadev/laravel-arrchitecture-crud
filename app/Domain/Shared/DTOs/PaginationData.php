<?php

declare(strict_types=1);

namespace App\Domain\Shared\DTOs;

readonly class PaginationData
{
    public function __construct(
        public int $page = 1,
        public int $perPage = 15,
        public ?string $sortBy = null,
        public string $sortDirection = 'asc',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            page: (int) ($data['page'] ?? 1),
            perPage: (int) ($data['per_page'] ?? 15),
            sortBy: $data['sort_by'] ?? null,
            sortDirection: $data['sort_direction'] ?? 'asc',
        );
    }

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'per_page' => $this->perPage,
            'sort_by' => $this->sortBy,
            'sort_direction' => $this->sortDirection,
        ];
    }
}
