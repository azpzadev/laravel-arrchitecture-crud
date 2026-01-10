<?php

declare(strict_types=1);

namespace App\Domain\Shared\DTOs;

/**
 * Base Data Transfer Object for common filter parameters.
 *
 * Provides reusable filter structure for list queries including
 * search, status filtering, date ranges, and pagination.
 *
 * @property-read string|null $search Search term for filtering
 * @property-read string|null $status Status filter value
 * @property-read string|null $startDate Start date for date range filter
 * @property-read string|null $endDate End date for date range filter
 * @property-read PaginationData|null $pagination Pagination parameters
 */
readonly class FilterData
{
    /**
     * Create a new FilterData instance.
     *
     * @param string|null $search Search term
     * @param string|null $status Status filter
     * @param string|null $startDate Start date (Y-m-d format)
     * @param string|null $endDate End date (Y-m-d format)
     * @param PaginationData|null $pagination Pagination settings
     */
    public function __construct(
        public ?string $search = null,
        public ?string $status = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?PaginationData $pagination = null,
    ) {}

    /**
     * Create a FilterData instance from an array.
     *
     * @param array{search?: string, status?: string, start_date?: string, end_date?: string, page?: int, per_page?: int, sort_by?: string, sort_direction?: string} $data The filter data array
     * @return self A new FilterData instance
     */
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

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, mixed> The filter data as an array (null values excluded)
     */
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
