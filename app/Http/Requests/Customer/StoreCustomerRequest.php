<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use App\Domain\Customer\DTOs\CustomerData;
use App\Domain\Customer\Enums\CustomerStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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

    public function toDto(): CustomerData
    {
        return CustomerData::fromArray($this->validated());
    }
}
