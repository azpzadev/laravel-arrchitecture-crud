<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use App\Domain\Customer\DTOs\CustomerData;
use App\Domain\Customer\Enums\CustomerStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for updating an existing customer.
 *
 * Validates customer data for the update endpoint with unique
 * email validation that excludes the current customer.
 *
 * @method CustomerData toDto() Convert validated data to DTO
 */
class UpdateCustomerRequest extends FormRequest
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
     * Get the validation rules for customer update.
     *
     * @return array<string, array<int, mixed>> Validation rules
     */
    public function rules(): array
    {
        $customerId = $this->route('customer')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($customerId),
            ],
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
