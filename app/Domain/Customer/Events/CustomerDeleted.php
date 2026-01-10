<?php

declare(strict_types=1);

namespace App\Domain\Customer\Events;

use App\Infrastructure\Models\Customer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a customer is deleted.
 *
 * This event is fired after a customer is soft-deleted or
 * permanently deleted from the system.
 *
 * @property Customer $customer The deleted customer
 * @property bool $forceDeleted Whether the customer was permanently deleted
 */
class CustomerDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new CustomerDeleted event instance.
     *
     * @param Customer $customer The deleted customer
     * @param bool $forceDeleted True if permanently deleted, false if soft-deleted
     */
    public function __construct(
        public Customer $customer,
        public bool $forceDeleted = false
    ) {}
}
