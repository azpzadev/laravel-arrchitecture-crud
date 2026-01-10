<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use App\Domain\Customer\DTOs\CustomerFilterData;
use App\Domain\Customer\Enums\CustomerStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for listing customers with filters.
 *
 * Validates and sanitizes query parameters for the customer index endpoint.
 *
 * @method CustomerFilterData toDto() Convert validated data to DTO
 */
class IndexCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Always true (authorization handled elsewhere)
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for customer listing.
     *
     * @return array<string, array<int, mixed>> Validation rules
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(CustomerStatus::values())],
            'company' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'with_trashed' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['nullable', 'string', Rule::in(['name', 'email', 'created_at', 'updated_at'])],
            'sort_direction' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ];
    }

    /**
     * Convert the validated request data to a CustomerFilterData DTO.
     *
     * @return CustomerFilterData The filter data object
     */
    public function toDto(): CustomerFilterData
    {
        return CustomerFilterData::fromArray($this->validated());
    }
}
