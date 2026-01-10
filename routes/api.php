<?php

declare(strict_types=1);

use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\ValidateApiToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Apply global API middleware
Route::middleware([ForceJsonResponse::class, ValidateApiToken::class])->group(function () {

    // API Version 1
    Route::prefix('v1')->name('api.v1.')->group(function () {
        require __DIR__ . '/api/v1/auth.php';
        require __DIR__ . '/api/v1/customers.php';

        // Add more v1 routes here
        // require __DIR__ . '/api/v1/orders.php';
        // require __DIR__ . '/api/v1/products.php';
    });

    // API Version 2 (future)
    // Route::prefix('v2')->name('api.v2.')->group(function () {
    //     require __DIR__ . '/api/v2/customers.php';
    // });

});

// Health check endpoint (outside middleware for monitoring)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('api.health');
