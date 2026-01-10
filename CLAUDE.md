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

## Coding Standards

### PHPDoc Documentation

Every file and function MUST have proper PHPDoc documentation explaining what it does.

#### Class Documentation
```php
/**
 * Handles customer creation with validation and event dispatching.
 *
 * This action validates customer data, checks for duplicates,
 * creates the customer record, and dispatches the CustomerCreated event.
 */
class CreateCustomerAction
{
    // ...
}
```

#### Method Documentation
```php
/**
 * Execute the customer creation process.
 *
 * @param CustomerData $data The validated customer data transfer object
 * @return Customer The newly created customer model
 *
 * @throws CustomerAlreadyExistsException When email already exists
 */
public function execute(CustomerData $data): Customer
{
    // ...
}
```

#### Property Documentation
```php
/**
 * @var CustomerRepositoryInterface The customer repository instance
 */
private CustomerRepositoryInterface $repository;
```

#### Interface Method Annotations
```php
/**
 * @method array validate(array $rules, ...$params) Validate request data against rules
 * @method array validated() Get the validated request data
 * @method bool hasValidSignature(bool $absolute = true) Check if request has valid signature
 */
```

#### DTO Documentation
```php
/**
 * Data Transfer Object for customer information.
 *
 * @property-read string $name Customer's full name
 * @property-read string $email Customer's email address
 * @property-read string|null $phone Customer's phone number (optional)
 */
readonly class CustomerData
{
    /**
     * Create a new CustomerData instance.
     *
     * @param string $name Customer's full name
     * @param string $email Customer's email address
     * @param string|null $phone Customer's phone number
     */
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone = null,
    ) {}

    /**
     * Create instance from array data.
     *
     * @param array{name: string, email: string, phone?: string|null} $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        // ...
    }
}
```

#### Repository Interface Documentation
```php
/**
 * Contract for customer data persistence operations.
 *
 * Defines the interface for customer repository implementations,
 * providing methods for CRUD operations and custom queries.
 */
interface CustomerRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find a customer by their email address.
     *
     * @param string $email The email address to search for
     * @return Customer|null The customer if found, null otherwise
     */
    public function findByEmail(string $email): ?Customer;
}
```

#### Controller Documentation
```php
/**
 * Handles HTTP requests for customer management.
 *
 * @group Customers
 * @authenticated
 */
class CustomerController extends Controller
{
    /**
     * Display a paginated list of customers.
     *
     * @param IndexCustomerRequest $request The validated index request
     * @return JsonResponse Paginated customer list
     *
     * @response 200 {
     *   "success": true,
     *   "data": [...],
     *   "meta": {"current_page": 1, "total": 100}
     * }
     */
    public function index(IndexCustomerRequest $request): JsonResponse
    {
        // ...
    }
}
```

#### Request Documentation
```php
/**
 * Validates and transforms customer creation request.
 *
 * @property string $name Customer name (required, max 255)
 * @property string $email Customer email (required, unique)
 * @property string|null $phone Customer phone (optional)
 */
class StoreCustomerRequest extends FormRequest
{
    /**
     * Get the validation rules for the request.
     *
     * @return array<string, array<int, string|Rule>>
     */
    public function rules(): array
    {
        // ...
    }

    /**
     * Convert validated request data to CustomerData DTO.
     *
     * @return CustomerData
     */
    public function toDto(): CustomerData
    {
        // ...
    }
}
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
- **Middleware/**: API middleware (ValidateApiToken, ForceJsonResponse)

### Routes
- **routes/api.php**: Main API routes (health, info)
- **routes/api/v1.php**: V1 route aggregator
- **routes/api/v1/**: Version 1 API endpoints (auth.php, customers.php)

### Providers
- **RouteServiceProvider**: Configures routes and rate limiting
- **RepositoryServiceProvider**: Binds repository interfaces to implementations
- **DomainServiceProvider**: Registers domain events, listeners, and policies

## Authentication

Uses Laravel Sanctum for token-based authentication with username/password login.

### API Token Header (x-api-token)
All API requests require a static API token in the header (configurable):
```bash
# .env
API_TOKEN_HEADER=x-api-token
API_TOKEN=your-secret-token
```

### Auth Endpoints
```
POST /api/v1/login       # Login with username/password, returns Bearer token
POST /api/v1/logout      # Logout current device (requires auth)
POST /api/v1/logout-all  # Logout all devices (requires auth)
GET  /api/v1/me          # Get current user (requires auth)
```

### Login Request
```json
{
    "username": "johndoe",
    "password": "password123",
    "device_name": "mobile"
}
```

### Login Response
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": { "uuid": "...", "name": "...", "username": "...", "email": "..." },
        "token": { "access_token": "1|abc123...", "token_type": "Bearer" }
    }
}
```

### Using Bearer Token
After login, include the token in subsequent requests:
```
Authorization: Bearer 1|abc123...
```

## Key Patterns

### Repository Pattern
All data access goes through repository interfaces:
```php
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
return ApiResponse::unauthorized('Message');
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

10. Add routes in `routes/api/v1/{domain}.php` and include in `routes/api/v1.php`

11. Create migration and factory in `database/`

12. Add tests in `tests/Feature/Api/V1/` and `tests/Unit/Domain/{DomainName}/`

## Database

- Uses PostgreSQL (psql)

## Testing

- Uses Pest (not PHPUnit syntax)
- Feature tests in `tests/Feature/Api/V1/`
- Unit tests in `tests/Unit/Domain/{DomainName}/`
- Uses `RefreshDatabase` trait for database tests
- User factory: `User::factory()->withPassword('secret')->create()`
- Customer factory: `Customer::factory()->active()->create()`
