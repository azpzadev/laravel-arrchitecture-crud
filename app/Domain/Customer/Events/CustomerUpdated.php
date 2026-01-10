<?php

declare(strict_types=1);

namespace App\Domain\Customer\Events;

use App\Infrastructure\Models\Customer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a customer is updated.
 *
 * This event is fired after a customer record is modified
 * and includes the changed attributes for audit purposes.
 *
 * @property Customer $customer The updated customer
 * @property array $changedAttributes The attributes that were changed
 */
class CustomerUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new CustomerUpdated event instance.
     *
     * @param Customer $customer The updated customer
     * @param array $changedAttributes Key-value pairs of changed attributes
     */
    public function __construct(
        public Customer $customer,
        public array $changedAttributes = []
    ) {}
}
