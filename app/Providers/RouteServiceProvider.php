<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\ValidateApiToken;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function (): void {
            // API Version 1 Routes
            Route::middleware(['api', ForceJsonResponse::class, ValidateApiToken::class])
                ->prefix('api/v1')
                ->name('api.v1.')
                ->group(base_path('routes/api/v1.php'));

            // Main API Routes (health check, etc.)
            Route::middleware(['api'])
                ->prefix('api')
                ->name('api.')
                ->group(base_path('routes/api.php'));

            // Web Routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Webhook Routes (no auth, no rate limiting)
            // Route::prefix('api/webhook')
            //     ->name('webhook.')
            //     ->group(base_path('routes/webhook.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Default API rate limiter
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many requests. Please try again later.',
                        'error_code' => 'TOO_MANY_REQUESTS',
                    ], 429);
                });
        });

        // Stricter rate limit for authentication endpoints
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many login attempts. Please try again later.',
                        'error_code' => 'TOO_MANY_ATTEMPTS',
                    ], 429);
                });
        });

        // Rate limit for sensitive operations
        RateLimiter::for('sensitive', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'Rate limit exceeded for sensitive operations.',
                        'error_code' => 'RATE_LIMIT_EXCEEDED',
                    ], 429);
                });
        });
    }
}
