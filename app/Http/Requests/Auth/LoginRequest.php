<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Domain\Auth\DTOs\LoginData;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for user login.
 *
 * Validates login credentials for the authentication endpoint.
 *
 * @method LoginData toDto() Convert validated data to DTO
 */
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Always true (authentication happens after validation)
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for login.
     *
     * @return array<string, array<int, string>> Validation rules
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Convert the validated request data to a LoginData DTO.
     *
     * @return LoginData The login credentials data object
     */
    public function toDto(): LoginData
    {
        return LoginData::fromArray($this->validated());
    }
}
