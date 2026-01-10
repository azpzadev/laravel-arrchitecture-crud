<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use App\Domain\Customer\DTOs\CustomerData;
use App\Domain\Customer\Enums\CustomerStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for creating a new customer.
 *
 * Validates customer data for the store endpoint.
 *
 * @method CustomerData toDto() Convert validated data to DTO
 */
class StoreCustomerRequest extends FormRequest
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
     * Get the validation rules for customer creation.
     *
     * @return array<string, array<int, mixed>> Validation rules
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'company' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(CustomerStatus::values())],
            'metadata' => ['nullable', 'array'],
        ];
    }

    /**
     * Convert the validated request data to a CustomerData DTO.
     *
     * @return CustomerData The customer data object
     */
    public function toDto(): CustomerData
    {
        return CustomerData::fromArray($this->validated());
    }
}
