<?php

declare(strict_types=1);

namespace App\Domain\Customer\Listeners;

use App\Domain\Customer\Events\CustomerCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendCustomerWelcomeNotification implements ShouldQueue
{
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

    public function failed(CustomerCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to send welcome notification', [
            'customer_id' => $event->customer->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
