<?php

declare(strict_types=1);

namespace App\Domain\Customer\Events;

use App\Infrastructure\Models\Customer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a new customer is created.
 *
 * This event is fired after a customer record is successfully
 * persisted and can be used for notifications, logging, or analytics.
 *
 * @property Customer $customer The newly created customer
 */
class CustomerCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new CustomerCreated event instance.
     *
     * @param Customer $customer The newly created customer
     */
    public function __construct(
        public Customer $customer
    ) {}
}
