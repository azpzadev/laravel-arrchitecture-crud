<?php

declare(strict_types=1);

namespace App\Domain\Customer\DTOs;

use App\Domain\Customer\Enums\CustomerStatus;
use App\Domain\Shared\DTOs\PaginationData;

/**
 * Data Transfer Object for customer list filtering.
 *
 * Encapsulates filter parameters for querying and paginating
 * customer records.
 *
 * @property-read string|null $search Search term for name/email filtering
 * @property-read CustomerStatus|null $status Filter by customer status
 * @property-read string|null $company Filter by company name
 * @property-read string|null $startDate Filter customers created after this date
 * @property-read string|null $endDate Filter customers created before this date
 * @property-read bool|null $withTrashed Include soft-deleted customers
 * @property-read PaginationData|null $pagination Pagination parameters
 */
readonly class CustomerFilterData
{
    /**
     * Create a new CustomerFilterData instance.
     *
     * @param string|null $search Search term for name/email
     * @param CustomerStatus|null $status Filter by status
     * @param string|null $company Filter by company
     * @param string|null $startDate Start date filter (Y-m-d format)
     * @param string|null $endDate End date filter (Y-m-d format)
     * @param bool|null $withTrashed Include soft-deleted records
     * @param PaginationData|null $pagination Pagination settings
     */
    public function __construct(
        public ?string $search = null,
        public ?CustomerStatus $status = null,
        public ?string $company = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?bool $withTrashed = false,
        public ?PaginationData $pagination = null,
    ) {}

    /**
     * Create a CustomerFilterData instance from an array.
     *
     * @param array{search?: string, status?: string, company?: string, start_date?: string, end_date?: string, with_trashed?: bool|string, page?: int, per_page?: int, sort_by?: string, sort_order?: string} $data The filter data array
     * @return self A new CustomerFilterData instance
     */
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

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, mixed> The filter data as an array (null values excluded)
     */
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
