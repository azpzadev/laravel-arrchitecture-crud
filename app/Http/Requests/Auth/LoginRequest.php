<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Domain\Auth\DTOs\LoginData;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toDto(): LoginData
    {
        return LoginData::fromArray($this->validated());
    }
}
