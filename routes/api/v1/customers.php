<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\CustomerController;
use Illuminate\Support\Facades\Route;

// All customer routes require authentication
Route::prefix('customers')->name('customers.')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::post('/', [CustomerController::class, 'store'])->name('store');
    Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
    Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
    Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
    Route::post('/{customer}/restore', [CustomerController::class, 'restore'])
        ->name('restore')
        ->withTrashed();
    Route::delete('/{customer}/force', [CustomerController::class, 'forceDelete'])
        ->name('force-delete')
        ->withTrashed();
});
