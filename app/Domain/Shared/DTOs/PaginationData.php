<?php

declare(strict_types=1);

namespace App\Domain\Shared\DTOs;

/**
 * Data Transfer Object for pagination parameters.
 *
 * Provides a consistent structure for handling pagination
 * across list queries and API responses.
 *
 * @property-read int $page The current page number (1-indexed)
 * @property-read int $perPage Number of items per page
 * @property-read string|null $sortBy Column name to sort by
 * @property-read string $sortDirection Sort direction ('asc' or 'desc')
 */
readonly class PaginationData
{
    /**
     * Create a new PaginationData instance.
     *
     * @param int $page The current page number
     * @param int $perPage Number of items per page
     * @param string|null $sortBy Column to sort by
     * @param string $sortDirection Sort direction ('asc' or 'desc')
     */
    public function __construct(
        public int $page = 1,
        public int $perPage = 15,
        public ?string $sortBy = null,
        public string $sortDirection = 'asc',
    ) {}

    /**
     * Create a PaginationData instance from an array.
     *
     * @param array{page?: int|string, per_page?: int|string, sort_by?: string, sort_direction?: string} $data The pagination data array
     * @return self A new PaginationData instance
     */
    public static function fromArray(array $data): self
    {
        return new self(
            page: (int) ($data['page'] ?? 1),
            perPage: (int) ($data['per_page'] ?? 15),
            sortBy: $data['sort_by'] ?? null,
            sortDirection: $data['sort_direction'] ?? 'asc',
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array{page: int, per_page: int, sort_by: string|null, sort_direction: string} The pagination data as an array
     */
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
