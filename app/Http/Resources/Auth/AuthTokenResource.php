<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API resource for authentication token data.
 *
 * Defines the JSON structure for token data in login responses.
 *
 * @mixin \App\Domain\Auth\DTOs\AuthTokenData
 */
class AuthTokenResource extends JsonResource
{
    /**
     * Transform the token data into an array.
     *
     * @param Request $request The incoming request
     * @return array<string, string|null> The transformed token data
     */
    public function toArray(Request $request): array
    {
        return [
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
            'expires_at' => $this->expiresAt,
        ];
    }
}
