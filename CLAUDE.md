# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 API backend using Domain-Driven Design (DDD) architecture with Pest for testing.

## Common Commands

### Development
```bash
composer dev         # Start all dev services (server, queue, logs) concurrently
php artisan serve    # Start Laravel development server only
```

### Setup
```bash
composer setup       # Full setup: install deps, copy .env, generate key, migrate
```

### Testing
```bash
composer test                          # Run all tests (clears config cache first)
php artisan test                       # Run all tests directly
php artisan test --filter=TestName     # Run a specific test
php artisan test tests/Feature         # Run feature tests only
php artisan test tests/Unit            # Run unit tests only
```

### Code Quality
```bash
./vendor/bin/pint    # Run Laravel Pint code style fixer
```

### Database
```bash
php artisan migrate              # Run migrations
php artisan migrate:fresh        # Drop all tables and re-run migrations
php artisan db:seed              # Run database seeders
```

## Architecture

This project follows a Domain-Driven Design (DDD) layered architecture:

### Domain Layer (`app/Domain/`)
Contains business logic organized by bounded contexts:
- **Actions/**: Single-responsibility classes for operations (Create, Update, Delete, Restore)
- **Services/**: Orchestrate actions and repository calls
- **DTOs/**: Data Transfer Objects for type-safe data passing (`fromArray()`, `toArray()`)
- **Events/**: Domain events dispatched after operations
- **Listeners/**: Event listeners for side effects
- **Enums/**: Domain enums with helper methods
- **Policies/**: Authorization policies
- **Exceptions/**: Domain-specific exceptions
- **Shared/**: Cross-domain utilities (DTOs, Traits, ValueObjects, Exceptions)

### Infrastructure Layer (`app/Infrastructure/`)
Handles persistence and external concerns:
- **Models/**: Eloquent models (separate from domain logic)
- **Repositories/Contracts/**: Repository interfaces defining data access contracts
- **Repositories/Eloquent/**: Eloquent implementations of repository interfaces
- **Persistence/Traits/**: Reusable traits (Filterable, Sortable, Searchable)
- **Persistence/Scopes/**: Global query scopes

### HTTP Layer (`app/Http/`)
API layer:
- **Controllers/Api/V1/**: Thin controllers that delegate to domain services
- **Requests/**: Form requests with `toDto()` method for conversion to domain DTOs
- **Resources/**: API resources for JSON response formatting
- **Responses/**: Standardized API response helpers (`ApiResponse`)
- **Middleware/**: API middleware

### Routes
- **routes/api.php**: Main API router with versioning
- **routes/api/v1/**: Version 1 API endpoints

### Providers
- **RepositoryServiceProvider**: Binds repository interfaces to implementations
- **DomainServiceProvider**: Registers domain events, listeners, and policies

## Key Patterns

### Repository Pattern
All data access goes through repository interfaces:
```php
// Interface
interface CustomerRepositoryInterface extends BaseRepositoryInterface {
    public function findByEmail(string $email): ?Customer;
}

// Binding in RepositoryServiceProvider
CustomerRepositoryInterface::class => CustomerRepository::class
```

### DTO Pattern
Use DTOs for type-safe data passing:
```php
$data = CustomerData::fromArray($request->validated());
$customer = $this->service->create($data);
```

### Form Request to DTO
Requests provide `toDto()` method:
```php
class StoreCustomerRequest extends FormRequest {
    public function toDto(): CustomerData {
        return CustomerData::fromArray($this->validated());
    }
}
```

### Action Classes
Each operation is a separate action class:
```php
class CreateCustomerAction {
    public function execute(CustomerData $data): Customer { ... }
}
```

### Service Layer
Services coordinate actions:
```php
class CustomerService {
    public function create(CustomerData $data): Customer {
        return $this->createAction->execute($data);
    }
}
```

### API Responses
Use `ApiResponse` helper for consistent responses:
```php
return ApiResponse::success($data, 'Message', 200);
return ApiResponse::created(new CustomerResource($customer));
return ApiResponse::paginated($paginator, CustomerResource::class);
return ApiResponse::error('Message', 400, $errors, 'ERROR_CODE');
```

## Adding a New Domain

1. Create domain structure under `app/Domain/{DomainName}/`:
   - Actions/, DTOs/, Services/, Events/, Listeners/, Enums/, Policies/, Exceptions/

2. Create model in `app/Infrastructure/Models/`

3. Create repository interface in `app/Infrastructure/Repositories/Contracts/`

4. Create Eloquent repository in `app/Infrastructure/Repositories/Eloquent/`

5. Register binding in `RepositoryServiceProvider`:
   ```php
   protected array $repositories = [
       CustomerRepositoryInterface::class => CustomerRepository::class,
       NewDomainRepositoryInterface::class => NewDomainRepository::class,
   ];
   ```

6. Register events/listeners in `DomainServiceProvider`

7. Create controller in `app/Http/Controllers/Api/V1/`

8. Create requests in `app/Http/Requests/{DomainName}/`

9. Create resources in `app/Http/Resources/{DomainName}/`

10. Add routes in `routes/api/v1/{domain}.php` and include in `routes/api.php`

11. Create migration and factory in `database/`

12. Add tests in `tests/Feature/Api/V1/` and `tests/Unit/Domain/{DomainName}/`

## Database

- Uses PostgreSQL (psql)

## Testing

- Uses Pest (not PHPUnit syntax)
- Feature tests in `tests/Feature/Api/V1/`
- Unit tests in `tests/Unit/Domain/{DomainName}/`
- Uses `RefreshDatabase` trait for database tests
- Customer factory available: `Customer::factory()->active()->create()`
