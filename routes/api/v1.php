<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
|
| All API version 1 routes are defined here. Routes are loaded by the
| RouteServiceProvider with 'api/v1' prefix and api middleware.
|
*/

// Auth Routes
require __DIR__ . '/v1/auth.php';

// Customer Routes
require __DIR__ . '/v1/customers.php';

// Add more v1 domain routes here
// require __DIR__ . '/v1/orders.php';
// require __DIR__ . '/v1/products.php';
