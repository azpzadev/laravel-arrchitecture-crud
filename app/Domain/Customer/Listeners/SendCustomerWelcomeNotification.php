<?php

declare(strict_types=1);

namespace App\Domain\Customer\Listeners;

use App\Domain\Customer\Events\CustomerCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener that sends a welcome notification to newly created customers.
 *
 * This listener is queued and handles the CustomerCreated event
 * to send welcome emails or other notifications to new customers.
 */
class SendCustomerWelcomeNotification implements ShouldQueue
{
    /**
     * Handle the CustomerCreated event.
     *
     * Sends a welcome notification to the newly created customer.
     *
     * @param CustomerCreated $event The customer created event
     * @return void
     */
    public function handle(CustomerCreated $event): void
    {
        $customer = $event->customer;

        // Implement your notification logic here
        // e.g., Mail::to($customer->email)->send(new WelcomeEmail($customer));

        Log::info('Welcome notification sent to customer', [
            'customer_id' => $customer->id,
            'email' => $customer->email,
        ]);
    }

    /**
     * Handle a job failure.
     *
     * Logs the error when the notification fails to send.
     *
     * @param CustomerCreated $event The customer created event
     * @param \Throwable $exception The exception that caused the failure
     * @return void
     */
    public function failed(CustomerCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to send welcome notification', [
            'customer_id' => $event->customer->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
