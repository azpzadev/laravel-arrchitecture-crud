<?php

declare(strict_types=1);

namespace App\Domain\Customer\Events;

use App\Infrastructure\Models\Customer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Customer $customer,
        public array $changedAttributes = []
    ) {}
}
