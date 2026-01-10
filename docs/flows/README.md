# Application Flows

## Overview

This document illustrates the key application flows with sequence diagrams showing how requests are processed through the system layers.

---

## Request Lifecycle

### General Request Flow

```mermaid
sequenceDiagram
    participant Client
    participant Middleware
    participant Controller
    participant FormRequest
    participant Service
    participant Action
    participant Repository
    participant Database

    Client->>Middleware: HTTP Request
    Middleware->>Middleware: ValidateApiToken
    Middleware->>Middleware: ForceJsonResponse
    Middleware->>Middleware: Sanctum Auth
    Middleware->>Controller: Authenticated Request
    Controller->>FormRequest: Validate Input
    FormRequest->>FormRequest: validate()
    FormRequest->>FormRequest: toDto()
    FormRequest-->>Controller: DTO
    Controller->>Service: Call Method(DTO)
    Service->>Action: execute(DTO)
    Action->>Repository: Data Operation
    Repository->>Database: Query
    Database-->>Repository: Result
    Repository-->>Action: Model
    Action->>Action: Dispatch Events
    Action-->>Service: Model
    Service-->>Controller: Model
    Controller->>Controller: Create Resource
    Controller-->>Client: JSON Response
```

---

## Authentication Flows

### Login Flow

```mermaid
sequenceDiagram
    participant Client
    participant ValidateApiToken
    participant AuthController
    participant LoginRequest
    participant AuthService
    participant LoginAction
    participant UserRepository
    participant Database
    participant Sanctum

    Client->>ValidateApiToken: POST /login + x-api-token
    ValidateApiToken->>ValidateApiToken: Verify API Token
    ValidateApiToken->>AuthController: Request
    AuthController->>LoginRequest: Validate
    LoginRequest->>LoginRequest: rules()
    LoginRequest-->>AuthController: LoginData DTO
    AuthController->>AuthService: login(LoginData)
    AuthService->>LoginAction: execute(LoginData)
    LoginAction->>UserRepository: findByUsername()
    UserRepository->>Database: SELECT * FROM users
    Database-->>UserRepository: User record
    UserRepository-->>LoginAction: User model
    LoginAction->>LoginAction: Verify password
    alt Invalid credentials
        LoginAction-->>AuthController: InvalidCredentialsException
        AuthController-->>Client: 401 Unauthorized
    else Valid credentials
        LoginAction->>Sanctum: createToken()
        Sanctum-->>LoginAction: Token
        LoginAction->>LoginAction: event(UserLoggedIn)
        LoginAction-->>AuthService: AuthTokenData
        AuthService-->>AuthController: AuthTokenData
        AuthController->>AuthController: AuthTokenResource
        AuthController-->>Client: 200 OK + Token
    end
```

### Token Validation Flow

```mermaid
sequenceDiagram
    participant Client
    participant ValidateApiToken
    participant SanctumAuth
    participant Controller

    Client->>ValidateApiToken: Request + x-api-token + Bearer
    ValidateApiToken->>ValidateApiToken: Check x-api-token header
    alt Missing/Invalid API Token
        ValidateApiToken-->>Client: 401 Invalid API Token
    else Valid API Token
        ValidateApiToken->>SanctumAuth: Continue
        SanctumAuth->>SanctumAuth: Validate Bearer token
        alt Invalid Bearer Token
            SanctumAuth-->>Client: 401 Unauthenticated
        else Valid Bearer Token
            SanctumAuth->>Controller: Authenticated Request
            Controller-->>Client: Response
        end
    end
```

### Logout Flow

```mermaid
sequenceDiagram
    participant Client
    participant AuthController
    participant AuthService
    participant LogoutAction
    participant Sanctum
    participant Database

    Client->>AuthController: POST /logout + Bearer Token
    AuthController->>AuthService: logout(User)
    AuthService->>LogoutAction: execute(User)
    LogoutAction->>Sanctum: currentAccessToken()
    Sanctum-->>LogoutAction: Token
    LogoutAction->>Database: DELETE token
    LogoutAction->>LogoutAction: event(UserLoggedOut)
    LogoutAction-->>AuthService: true
    AuthService-->>AuthController: true
    AuthController-->>Client: 200 OK
```

---

## Customer CRUD Flows

### Create Customer Flow

```mermaid
sequenceDiagram
    participant Client
    participant CustomerController
    participant StoreCustomerRequest
    participant CustomerService
    participant CreateCustomerAction
    participant CustomerRepository
    participant Database

    Client->>CustomerController: POST /customers
    CustomerController->>StoreCustomerRequest: Validate
    StoreCustomerRequest->>StoreCustomerRequest: rules()
    StoreCustomerRequest->>StoreCustomerRequest: toDto()
    StoreCustomerRequest-->>CustomerController: CustomerData
    CustomerController->>CustomerService: create(CustomerData)
    CustomerService->>CreateCustomerAction: execute(CustomerData)
    CreateCustomerAction->>CustomerRepository: existsByEmail()
    CustomerRepository->>Database: SELECT EXISTS
    Database-->>CustomerRepository: false
    alt Email exists
        CreateCustomerAction-->>Client: 409 Conflict
    else Email unique
        CreateCustomerAction->>CustomerRepository: create(data)
        CustomerRepository->>Database: INSERT
        Database-->>CustomerRepository: Customer
        CreateCustomerAction->>CreateCustomerAction: event(CustomerCreated)
        CreateCustomerAction-->>CustomerService: Customer
        CustomerService-->>CustomerController: Customer
        CustomerController->>CustomerController: CustomerResource
        CustomerController-->>Client: 201 Created
    end
```

### Update Customer Flow

```mermaid
sequenceDiagram
    participant Client
    participant CustomerController
    participant UpdateCustomerRequest
    participant CustomerService
    participant UpdateCustomerAction
    participant CustomerRepository
    participant Database

    Client->>CustomerController: PUT /customers/{uuid}
    CustomerController->>CustomerController: Route Model Binding
    CustomerController->>UpdateCustomerRequest: Validate
    UpdateCustomerRequest->>UpdateCustomerRequest: toDto()
    UpdateCustomerRequest-->>CustomerController: CustomerData
    CustomerController->>CustomerService: update(Customer, Data)
    CustomerService->>UpdateCustomerAction: execute(Customer, Data)
    alt Email changed
        UpdateCustomerAction->>CustomerRepository: existsByEmail()
        CustomerRepository->>Database: SELECT EXISTS
        alt Email taken
            UpdateCustomerAction-->>Client: 409 Conflict
        end
    end
    UpdateCustomerAction->>CustomerRepository: update(Customer, data)
    CustomerRepository->>Database: UPDATE
    Database-->>CustomerRepository: Customer
    UpdateCustomerAction->>UpdateCustomerAction: event(CustomerUpdated)
    UpdateCustomerAction-->>CustomerService: Customer
    CustomerService-->>CustomerController: Customer
    CustomerController-->>Client: 200 OK
```

### Delete Customer Flow

```mermaid
sequenceDiagram
    participant Client
    participant CustomerController
    participant CustomerService
    participant DeleteCustomerAction
    participant CustomerRepository
    participant Database

    Client->>CustomerController: DELETE /customers/{uuid}
    CustomerController->>CustomerController: Route Model Binding
    CustomerController->>CustomerService: delete(Customer, force)
    CustomerService->>DeleteCustomerAction: execute(Customer, force)
    alt Force Delete
        DeleteCustomerAction->>CustomerRepository: forceDelete()
        CustomerRepository->>Database: DELETE
    else Soft Delete
        DeleteCustomerAction->>CustomerRepository: delete()
        CustomerRepository->>Database: UPDATE deleted_at
    end
    DeleteCustomerAction->>DeleteCustomerAction: event(CustomerDeleted)
    DeleteCustomerAction-->>CustomerService: true
    CustomerService-->>CustomerController: true
    CustomerController-->>Client: 204 No Content
```

### List Customers Flow

```mermaid
sequenceDiagram
    participant Client
    participant CustomerController
    participant IndexCustomerRequest
    participant CustomerService
    participant CustomerRepository
    participant Database

    Client->>CustomerController: GET /customers?params
    CustomerController->>IndexCustomerRequest: Validate
    IndexCustomerRequest->>IndexCustomerRequest: toFilterDto()
    IndexCustomerRequest-->>CustomerController: CustomerFilterData
    CustomerController->>CustomerService: paginate(filters)
    CustomerService->>CustomerRepository: paginate(perPage, filters)
    CustomerRepository->>CustomerRepository: applyFilters()
    CustomerRepository->>CustomerRepository: applySearch()
    CustomerRepository->>CustomerRepository: applySorting()
    CustomerRepository->>Database: SELECT with pagination
    Database-->>CustomerRepository: Results + count
    CustomerRepository-->>CustomerService: LengthAwarePaginator
    CustomerService-->>CustomerController: Paginator
    CustomerController->>CustomerController: CustomerCollection
    CustomerController-->>Client: 200 OK + meta + links
```

---

## Event Flow

### Event Dispatching

```mermaid
sequenceDiagram
    participant Action
    participant EventDispatcher
    participant Listener
    participant Notification
    participant Queue

    Action->>EventDispatcher: event(CustomerCreated)
    EventDispatcher->>Listener: handle(CustomerCreated)

    alt Synchronous Listener
        Listener->>Listener: Process immediately
    else Queued Listener
        Listener->>Queue: Push to queue
        Queue-->>Queue: Process async
        Queue->>Notification: Send notification
    end
```

### Available Events

| Event | Trigger | Listeners |
|-------|---------|-----------|
| `UserLoggedIn` | Successful login | Logging, analytics |
| `UserLoggedOut` | Logout | Logging |
| `CustomerCreated` | New customer | Welcome notification |
| `CustomerUpdated` | Customer update | Sync, logging |
| `CustomerDeleted` | Customer delete | Cleanup, archive |

---

## Error Handling Flow

```mermaid
sequenceDiagram
    participant Client
    participant Middleware
    participant Controller
    participant ExceptionHandler
    participant ApiResponse

    Client->>Middleware: Request

    alt Validation Error
        Middleware->>ExceptionHandler: ValidationException
        ExceptionHandler->>ApiResponse: error(422)
        ApiResponse-->>Client: 422 + errors
    else Domain Exception
        Controller->>ExceptionHandler: DomainException
        ExceptionHandler->>ApiResponse: error(code)
        ApiResponse-->>Client: 4xx + error_code
    else Server Error
        Controller->>ExceptionHandler: Exception
        ExceptionHandler->>ApiResponse: error(500)
        ApiResponse-->>Client: 500 + message
    end
```

---

## Middleware Stack

```mermaid
graph TD
    A[Incoming Request] --> B[ForceJsonResponse]
    B --> C[ValidateApiToken]
    C --> D{API Token Valid?}
    D -->|No| E[401 Response]
    D -->|Yes| F[Sanctum Auth]
    F --> G{Token Valid?}
    G -->|No| H[401 Response]
    G -->|Yes| I[ThrottleRequests]
    I --> J{Rate Limit OK?}
    J -->|No| K[429 Response]
    J -->|Yes| L[Controller]
    L --> M[Response]
```
