<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

// Public auth routes (with rate limiting for security)
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:auth')
    ->name('auth.login');

// Protected auth routes (requires Sanctum authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('auth.logout-all');
    Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
});
