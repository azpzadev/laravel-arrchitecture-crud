# Testing Documentation

## Overview

The application uses Pest PHP for testing, providing an expressive and elegant syntax for writing tests.

---

## Testing Stack

| Tool | Version | Purpose |
|------|---------|---------|
| Pest | 3.x | Testing framework |
| PHPUnit | 11.x | Test runner |
| Laravel Testing | - | HTTP testing helpers |

---

## Directory Structure

```
tests/
├── Feature/
│   └── Api/
│       └── V1/
│           ├── AuthTest.php        # Authentication tests
│           └── CustomerTest.php    # Customer CRUD tests
├── Unit/
│   └── .gitkeep
├── Pest.php                        # Pest configuration
└── TestCase.php                    # Base test class
```

---

## Running Tests

### All Tests

```bash
# Run all tests
php artisan test

# With coverage
php artisan test --coverage

# Parallel execution
php artisan test --parallel
```

### Specific Tests

```bash
# Run single file
php artisan test tests/Feature/Api/V1/AuthTest.php

# Run by filter
php artisan test --filter=login

# Run by group
php artisan test --group=auth
```

### Pest Commands

```bash
# Run with Pest directly
./vendor/bin/pest

# Watch mode (requires fswatch)
./vendor/bin/pest --watch

# Type coverage
./vendor/bin/pest --type-coverage
```

---

## Configuration

### phpunit.xml

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="pgsql"/>
        <env name="DB_DATABASE" value="testing"/>
    </php>
</phpunit>
```

### tests/Pest.php

```php
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extends(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');
```

---

## Test Helpers

### API Token Header

All API requests need the `x-api-token` header:

```php
$this->withHeaders([
    'x-api-token' => config('api.token'),
])->postJson('/api/v1/login', $data);
```

### Authentication Helper

Login and get bearer token:

```php
protected function authenticatedUser(): array
{
    $user = User::factory()->create();

    $response = $this->withHeaders([
        'x-api-token' => config('api.token'),
    ])->postJson('/api/v1/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    return [
        'user' => $user,
        'token' => $response->json('data.token.access_token'),
    ];
}
```

### Authenticated Request

```php
$this->withHeaders([
    'x-api-token' => config('api.token'),
    'Authorization' => "Bearer {$token}",
])->getJson('/api/v1/me');
```

---

## Test Examples

### Authentication Tests

```php
<?php

use App\Infrastructure\Models\User;

describe('Login', function () {
    it('logs in with valid credentials', function () {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->withHeaders([
            'x-api-token' => config('api.token'),
        ])->postJson('/api/v1/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['uuid', 'name', 'email'],
                    'token' => ['access_token', 'token_type'],
                ],
            ]);
    });

    it('rejects invalid credentials', function () {
        $user = User::factory()->create();

        $response = $this->withHeaders([
            'x-api-token' => config('api.token'),
        ])->postJson('/api/v1/login', [
            'username' => $user->username,
            'password' => 'wrong-password',
        ]);

        $response->assertUnauthorized()
            ->assertJson([
                'success' => false,
                'error_code' => 'INVALID_CREDENTIALS',
            ]);
    });

    it('validates required fields', function () {
        $response = $this->withHeaders([
            'x-api-token' => config('api.token'),
        ])->postJson('/api/v1/login', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['username', 'password']);
    });
});

describe('Logout', function () {
    it('revokes current token', function () {
        ['user' => $user, 'token' => $token] = $this->authenticatedUser();

        $response = $this->withHeaders([
            'x-api-token' => config('api.token'),
            'Authorization' => "Bearer {$token}",
        ])->postJson('/api/v1/logout');

        $response->assertOk();

        // Token should be invalid now
        $this->withHeaders([
            'x-api-token' => config('api.token'),
            'Authorization' => "Bearer {$token}",
        ])->getJson('/api/v1/me')
            ->assertUnauthorized();
    });
});
```

### Customer CRUD Tests

```php
<?php

use App\Infrastructure\Models\Customer;
use App\Infrastructure\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test')->plainTextToken;
    $this->headers = [
        'x-api-token' => config('api.token'),
        'Authorization' => "Bearer {$this->token}",
    ];
});

describe('List Customers', function () {
    it('returns paginated customers', function () {
        Customer::factory()->count(20)->create();

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/v1/customers');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [['uuid', 'name', 'email']],
                'meta' => ['current_page', 'total', 'per_page'],
                'links',
            ]);
    });

    it('filters by status', function () {
        Customer::factory()->count(5)->create(['status' => 'active']);
        Customer::factory()->count(3)->create(['status' => 'inactive']);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/v1/customers?status=active');

        $response->assertOk();
        expect($response->json('meta.total'))->toBe(5);
    });

    it('searches by name or email', function () {
        Customer::factory()->create(['name' => 'John Doe']);
        Customer::factory()->create(['email' => 'john@example.com']);
        Customer::factory()->count(5)->create();

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/v1/customers?search=john');

        $response->assertOk();
        expect($response->json('meta.total'))->toBe(2);
    });
});

describe('Create Customer', function () {
    it('creates customer with valid data', function () {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
        ];

        $response = $this->withHeaders($this->headers)
            ->postJson('/api/v1/customers', $data);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ],
            ]);

        $this->assertDatabaseHas('customers', ['email' => 'john@example.com']);
    });

    it('rejects duplicate email', function () {
        Customer::factory()->create(['email' => 'john@example.com']);

        $response = $this->withHeaders($this->headers)
            ->postJson('/api/v1/customers', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ]);

        $response->assertConflict()
            ->assertJson(['error_code' => 'CUSTOMER_ALREADY_EXISTS']);
    });
});

describe('Update Customer', function () {
    it('updates customer fields', function () {
        $customer = Customer::factory()->create();

        $response = $this->withHeaders($this->headers)
            ->putJson("/api/v1/customers/{$customer->uuid}", [
                'name' => 'Updated Name',
            ]);

        $response->assertOk()
            ->assertJson(['data' => ['name' => 'Updated Name']]);
    });
});

describe('Delete Customer', function () {
    it('soft deletes customer', function () {
        $customer = Customer::factory()->create();

        $response = $this->withHeaders($this->headers)
            ->deleteJson("/api/v1/customers/{$customer->uuid}");

        $response->assertNoContent();
        $this->assertSoftDeleted('customers', ['uuid' => $customer->uuid]);
    });

    it('force deletes with parameter', function () {
        $customer = Customer::factory()->create();

        $response = $this->withHeaders($this->headers)
            ->deleteJson("/api/v1/customers/{$customer->uuid}?force=true");

        $response->assertNoContent();
        $this->assertDatabaseMissing('customers', ['uuid' => $customer->uuid]);
    });
});

describe('Restore Customer', function () {
    it('restores soft deleted customer', function () {
        $customer = Customer::factory()->trashed()->create();

        $response = $this->withHeaders($this->headers)
            ->postJson("/api/v1/customers/{$customer->uuid}/restore");

        $response->assertOk();
        expect($customer->fresh()->deleted_at)->toBeNull();
    });
});
```

---

## Testing Best Practices

### 1. Use Factories

```php
// Good
$customer = Customer::factory()->create();

// Avoid
$customer = Customer::create([...]);
```

### 2. Use RefreshDatabase

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
```

### 3. Test Both Success and Failure

```php
it('creates customer with valid data', function () { ... });
it('rejects invalid email format', function () { ... });
it('rejects duplicate email', function () { ... });
```

### 4. Use Descriptive Test Names

```php
// Good
it('returns 404 when customer not found');

// Avoid
it('test customer not found');
```

### 5. Assert Database State

```php
$this->assertDatabaseHas('customers', ['email' => $email]);
$this->assertDatabaseMissing('customers', ['email' => $email]);
$this->assertSoftDeleted('customers', ['uuid' => $uuid]);
```

### 6. Group Related Tests

```php
describe('Login', function () {
    it('succeeds with valid credentials');
    it('fails with invalid password');
    it('fails with inactive user');
});
```

---

## Code Coverage

### Generate Coverage Report

```bash
# HTML report
php artisan test --coverage-html coverage

# Text summary
php artisan test --coverage

# Minimum threshold
php artisan test --coverage --min=80
```

### Coverage Targets

| Layer | Target |
|-------|--------|
| Controllers | 90% |
| Services | 95% |
| Actions | 95% |
| Repositories | 85% |

---

## Continuous Integration

### GitHub Actions Test Job

```yaml
test:
  runs-on: ubuntu-latest

  services:
    postgres:
      image: postgres:15
      env:
        POSTGRES_USER: test
        POSTGRES_PASSWORD: test
        POSTGRES_DB: testing
      ports:
        - 5432:5432

  steps:
    - uses: actions/checkout@v4

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.3'
        extensions: pdo, pdo_pgsql
        coverage: xdebug

    - name: Install Dependencies
      run: composer install --no-interaction

    - name: Run Tests
      env:
        DB_CONNECTION: pgsql
        DB_DATABASE: testing
        DB_USERNAME: test
        DB_PASSWORD: test
      run: php artisan test --coverage --min=80
```
