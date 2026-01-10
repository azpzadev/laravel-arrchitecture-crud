<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API resource for authenticated user data.
 *
 * Defines the JSON structure for user data in authentication responses.
 *
 * @mixin \App\Infrastructure\Models\User
 */
class AuthUserResource extends JsonResource
{
    /**
     * Transform the user model into an array.
     *
     * @param Request $request The incoming request
     * @return array<string, mixed> The transformed user data
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
