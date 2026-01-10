<?php

declare(strict_types=1);

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API resource for transforming Customer models.
 *
 * Defines the JSON structure for customer data in API responses.
 *
 * @mixin \App\Infrastructure\Models\Customer
 */
class CustomerResource extends JsonResource
{
    /**
     * Transform the customer model into an array.
     *
     * @param Request $request The incoming request
     * @return array<string, mixed> The transformed customer data
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'company' => $this->company,
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
            ],
            'metadata' => $this->metadata,
            'is_active' => $this->isActive(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->when($this->deleted_at, fn() => $this->deleted_at?->toIso8601String()),
        ];
    }
}
