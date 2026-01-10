# Directory Structure

## Overview

This document provides a complete map of the project's directory structure and explains the purpose of each directory.

## Root Structure

```
├── app/
│   ├── Domain/              # Business logic layer
│   ├── Http/                # HTTP presentation layer
│   ├── Infrastructure/      # Data persistence layer
│   └── Providers/           # Service providers
├── bootstrap/               # Framework bootstrap
├── config/                  # Configuration files
├── database/
│   ├── factories/           # Model factories
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── docs/                    # Project documentation
├── public/                  # Public web root
├── resources/               # Views and assets
├── routes/
│   ├── api/                 # API route files
│   └── web.php              # Web routes
├── storage/                 # Logs, cache, uploads
├── tests/                   # Test suites
├── vendor/                  # Composer dependencies
├── .env                     # Environment config
├── CLAUDE.md                # AI assistant guidelines
├── composer.json            # PHP dependencies
└── phpunit.xml              # Test configuration
```

---

## Domain Layer (`app/Domain/`)

```
app/Domain/
├── Auth/                           # Authentication bounded context
│   ├── Actions/
│   │   ├── LoginAction.php         # Handle user login
│   │   └── LogoutAction.php        # Handle user logout
│   ├── DTOs/
│   │   ├── AuthTokenData.php       # Token response data
│   │   └── LoginData.php           # Login credentials data
│   ├── Events/
│   │   ├── UserLoggedIn.php        # Login event
│   │   └── UserLoggedOut.php       # Logout event
│   ├── Exceptions/
│   │   ├── InvalidApiTokenException.php
│   │   ├── InvalidCredentialsException.php
│   │   └── UserNotFoundException.php
│   └── Services/
│       └── AuthService.php         # Auth orchestration
│
├── Customer/                       # Customer bounded context
│   ├── Actions/
│   │   ├── CreateCustomerAction.php
│   │   ├── UpdateCustomerAction.php
│   │   ├── DeleteCustomerAction.php
│   │   └── RestoreCustomerAction.php
│   ├── DTOs/
│   │   ├── CustomerData.php        # Customer entity data
│   │   └── CustomerFilterData.php  # List filter data
│   ├── Enums/
│   │   └── CustomerStatus.php      # Status enumeration
│   ├── Events/
│   │   ├── CustomerCreated.php
│   │   ├── CustomerUpdated.php
│   │   └── CustomerDeleted.php
│   ├── Exceptions/
│   │   ├── CustomerNotFoundException.php
│   │   └── CustomerAlreadyExistsException.php
│   ├── Listeners/
│   │   └── SendCustomerWelcomeNotification.php
│   ├── Policies/
│   │   └── CustomerPolicy.php      # Authorization rules
│   └── Services/
│       └── CustomerService.php
│
└── Shared/                         # Cross-cutting concerns
    ├── DTOs/
    │   ├── FilterData.php          # Base filter DTO
    │   └── PaginationData.php      # Pagination parameters
    ├── Exceptions/
    │   └── DomainException.php     # Base domain exception
    ├── Traits/
    │   └── HasUuid.php             # UUID model trait
    └── ValueObjects/
        ├── Email.php               # Email value object
        └── Money.php               # Money value object
```

---

## HTTP Layer (`app/Http/`)

```
app/Http/
├── Controllers/
│   ├── Api/
│   │   └── V1/                     # API version 1
│   │       ├── AuthController.php
│   │       └── CustomerController.php
│   └── Controller.php              # Base controller
│
├── Middleware/
│   ├── ForceJsonResponse.php       # Force JSON Accept header
│   └── ValidateApiToken.php        # x-api-token validation
│
├── Requests/
│   ├── Auth/
│   │   └── LoginRequest.php
│   └── Customer/
│       ├── IndexCustomerRequest.php
│       ├── StoreCustomerRequest.php
│       └── UpdateCustomerRequest.php
│
├── Resources/
│   ├── Auth/
│   │   ├── AuthTokenResource.php
│   │   └── AuthUserResource.php
│   └── Customer/
│       ├── CustomerCollection.php
│       └── CustomerResource.php
│
└── Responses/
    └── ApiResponse.php             # Standardized responses
```

---

## Infrastructure Layer (`app/Infrastructure/`)

```
app/Infrastructure/
├── Models/
│   ├── Customer.php                # Customer Eloquent model
│   └── User.php                    # User Eloquent model
│
├── Persistence/
│   ├── Scopes/
│   │   └── ActiveScope.php         # Global active filter
│   └── Traits/
│       ├── Filterable.php          # Dynamic filtering
│       ├── Searchable.php          # Full-text search
│       └── Sortable.php            # Query sorting
│
└── Repositories/
    ├── Contracts/
    │   ├── BaseRepositoryInterface.php
    │   ├── CustomerRepositoryInterface.php
    │   └── UserRepositoryInterface.php
    └── Eloquent/
        ├── BaseRepository.php
        ├── CustomerRepository.php
        └── UserRepository.php
```

---

## Routes (`routes/`)

```
routes/
├── api/
│   └── v1/
│       ├── auth.php               # Authentication routes
│       └── customers.php          # Customer CRUD routes
├── api.php                        # API route loader
├── console.php                    # Artisan commands
└── web.php                        # Web routes
```

---

## Database (`database/`)

```
database/
├── factories/
│   ├── CustomerFactory.php
│   └── UserFactory.php
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 0001_01_01_000002_create_jobs_table.php
│   └── 2024_01_01_000001_create_customers_table.php
└── seeders/
    ├── DatabaseSeeder.php
    └── UserSeeder.php
```

---

## Tests (`tests/`)

```
tests/
├── Feature/
│   └── Api/
│       └── V1/
│           └── AuthTest.php       # Auth endpoint tests
├── Unit/
│   └── .gitkeep
├── Pest.php                       # Pest configuration
└── TestCase.php                   # Base test class
```

---

## Configuration (`config/`)

Key configuration files:

| File | Purpose |
|------|---------|
| `api.php` | API token settings |
| `app.php` | Application settings |
| `auth.php` | Authentication guards |
| `database.php` | Database connections |
| `sanctum.php` | Sanctum token settings |

---

## Naming Conventions

### Files

| Type | Convention | Example |
|------|------------|---------|
| Action | `{Verb}{Entity}Action.php` | `CreateCustomerAction.php` |
| DTO | `{Entity}Data.php` | `CustomerData.php` |
| Service | `{Entity}Service.php` | `CustomerService.php` |
| Repository | `{Entity}Repository.php` | `CustomerRepository.php` |
| Controller | `{Entity}Controller.php` | `CustomerController.php` |
| Request | `{Verb}{Entity}Request.php` | `StoreCustomerRequest.php` |
| Resource | `{Entity}Resource.php` | `CustomerResource.php` |
| Exception | `{Entity}{Problem}Exception.php` | `CustomerNotFoundException.php` |
| Event | `{Entity}{Action}.php` | `CustomerCreated.php` |
| Policy | `{Entity}Policy.php` | `CustomerPolicy.php` |

### Classes

| Type | Convention |
|------|------------|
| Interface | Suffix with `Interface` |
| Abstract | Prefix with `Base` or use `abstract` |
| Trait | Prefix with `Has` or descriptive verb |
| Enum | Singular noun |
