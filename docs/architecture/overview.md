# Architecture Overview

## Introduction

This application follows **Domain-Driven Design (DDD)** principles with a layered architecture that separates concerns and promotes maintainability, testability, and scalability.

## High-Level Architecture

```mermaid
graph TB
    subgraph "HTTP Layer"
        A[Controllers] --> B[Requests]
        A --> C[Resources]
        A --> D[Middleware]
    end

    subgraph "Domain Layer"
        E[Services] --> F[Actions]
        F --> G[DTOs]
        F --> H[Events]
        E --> I[Exceptions]
    end

    subgraph "Infrastructure Layer"
        J[Repositories] --> K[Models]
        K --> L[Database]
    end

    A --> E
    E --> J

    style A fill:#e1f5fe
    style E fill:#fff3e0
    style J fill:#e8f5e9
```

## Architecture Principles

### 1. Separation of Concerns
Each layer has a specific responsibility:
- **HTTP Layer**: Handles HTTP requests/responses
- **Domain Layer**: Contains business logic
- **Infrastructure Layer**: Handles data persistence

### 2. Dependency Inversion
- High-level modules don't depend on low-level modules
- Both depend on abstractions (interfaces)
- Repository interfaces in Domain, implementations in Infrastructure

### 3. Single Responsibility
- Each class has one reason to change
- Actions perform one business operation
- DTOs carry data between layers

## Layer Communication

```mermaid
sequenceDiagram
    participant Client
    participant Controller
    participant Service
    participant Action
    participant Repository
    participant Database

    Client->>Controller: HTTP Request
    Controller->>Controller: Validate (FormRequest)
    Controller->>Service: Call with DTO
    Service->>Action: Execute business logic
    Action->>Repository: Data operation
    Repository->>Database: Query/Persist
    Database-->>Repository: Result
    Repository-->>Action: Model/Collection
    Action-->>Service: Result
    Service-->>Controller: Result
    Controller-->>Client: JSON Response
```

## Bounded Contexts

The application is organized into bounded contexts (domains):

| Context | Purpose | Location |
|---------|---------|----------|
| **Auth** | Authentication & Authorization | `app/Domain/Auth/` |
| **Customer** | Customer management | `app/Domain/Customer/` |
| **Shared** | Cross-cutting concerns | `app/Domain/Shared/` |

## Key Design Decisions

### Why DDD?
- Clear separation between business logic and infrastructure
- Easier to understand and maintain
- Facilitates team collaboration
- Enables independent domain evolution

### Why Repository Pattern?
- Abstracts data access logic
- Makes domain layer database-agnostic
- Simplifies testing with mock repositories
- Single source of truth for queries

### Why Action Classes?
- Single responsibility for each business operation
- Easy to test in isolation
- Clear naming indicates purpose
- Reusable across controllers

### Why DTOs?
- Type-safe data transfer
- Decouples layers
- Self-documenting data structures
- Immutable by design (readonly)

## Next Steps

- [Layer Structure](./layers.md) - Detailed explanation of each layer
- [Design Patterns](./patterns.md) - Patterns used in the codebase
- [Directory Structure](./directory-structure.md) - File organization
