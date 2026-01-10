<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Customer\Events\CustomerCreated;
use App\Domain\Customer\Events\CustomerDeleted;
use App\Domain\Customer\Events\CustomerUpdated;
use App\Domain\Customer\Listeners\SendCustomerWelcomeNotification;
use App\Domain\Customer\Policies\CustomerPolicy;
use App\Infrastructure\Models\Customer;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Domain event listeners.
     *
     * @var array<class-string, array<class-string>>
     */
    protected array $listen = [
        CustomerCreated::class => [
            SendCustomerWelcomeNotification::class,
        ],
        CustomerUpdated::class => [
            // Add listeners here
        ],
        CustomerDeleted::class => [
            // Add listeners here
        ],
    ];

    /**
     * Model policies.
     *
     * @var array<class-string, class-string>
     */
    protected array $policies = [
        Customer::class => CustomerPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerEventListeners();
        $this->registerPolicies();
    }

    protected function registerEventListeners(): void
    {
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }

    protected function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
