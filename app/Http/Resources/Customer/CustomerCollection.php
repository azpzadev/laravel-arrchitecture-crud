<?php

declare(strict_types=1);

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * API resource collection for Customer models.
 *
 * Wraps a collection of CustomerResource instances for API responses.
 */
class CustomerCollection extends ResourceCollection
{
    /**
     * The resource that this collection collects.
     *
     * @var string
     */
    public $collects = CustomerResource::class;

    /**
     * Transform the customer collection into an array.
     *
     * @param Request $request The incoming request
     * @return array<string, mixed> The transformed collection data
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
