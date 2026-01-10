<?php

declare(strict_types=1);

namespace App\Providers;

use App\Infrastructure\Repositories\Contracts\CustomerRepositoryInterface;
use App\Infrastructure\Repositories\Contracts\UserRepositoryInterface;
use App\Infrastructure\Repositories\Eloquent\CustomerRepository;
use App\Infrastructure\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for repository bindings.
 *
 * Registers all repository interface to implementation bindings
 * for dependency injection throughout the application.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All repository bindings.
     *
     * Maps repository interfaces to their Eloquent implementations.
     *
     * @var array<class-string, class-string>
     */
    protected array $repositories = [
        UserRepositoryInterface::class => UserRepository::class,
        CustomerRepositoryInterface::class => CustomerRepository::class,
        // Add more repository bindings here
        // OrderRepositoryInterface::class => OrderRepository::class,
        // ProductRepositoryInterface::class => ProductRepository::class,
    ];

    /**
     * Register repository bindings.
     *
     * @return void
     */
    public function register(): void
    {
        foreach ($this->repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
