<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Main API routes loaded by RouteServiceProvider with 'api' prefix.
| These routes are outside versioning for common endpoints.
|
*/

// Health check endpoint (for monitoring, load balancers)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('health');

// API Info endpoint
Route::get('/', function () {
    return response()->json([
        'name' => config('app.name') . ' API',
        'version' => 'v1',
        'documentation' => url('/api/docs'),
    ]);
})->name('info');
