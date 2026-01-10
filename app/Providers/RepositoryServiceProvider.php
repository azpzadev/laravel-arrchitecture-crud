<?php

declare(strict_types=1);

namespace App\Providers;

use App\Infrastructure\Repositories\Contracts\CustomerRepositoryInterface;
use App\Infrastructure\Repositories\Eloquent\CustomerRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All repository bindings.
     *
     * @var array<class-string, class-string>
     */
    protected array $repositories = [
        CustomerRepositoryInterface::class => CustomerRepository::class,
        // Add more repository bindings here
        // OrderRepositoryInterface::class => OrderRepository::class,
        // ProductRepositoryInterface::class => ProductRepository::class,
    ];

    public function register(): void
    {
        foreach ($this->repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    public function boot(): void
    {
        //
    }
}
