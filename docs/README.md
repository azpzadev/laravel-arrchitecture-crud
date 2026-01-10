# Project Documentation

## Laravel DDD Architecture - API Backend

A Laravel 12 API backend application built with Domain-Driven Design (DDD) architecture, featuring modular bounded contexts, repository pattern, and comprehensive authentication.

---

## Table of Contents

### Getting Started
- [Quick Start Guide](./deployment/setup.md)
- [Environment Configuration](./deployment/configuration.md)
- [Development Workflow](./deployment/development.md)

### Architecture
- [Architecture Overview](./architecture/overview.md)
- [Layer Structure](./architecture/layers.md)
- [Design Patterns](./architecture/patterns.md)
- [Directory Structure](./architecture/directory-structure.md)

### API Reference
- [API Overview](./api/README.md)
- [Authentication](./api/authentication.md)
- [Customers](./api/customers.md)
- [Error Handling](./api/error-handling.md)

### Database
- [Database Overview](./database/README.md)
- [Schema & ERD](./database/schema.md)
- [Migrations](./database/migrations.md)

### Application Flows
- [Flow Diagrams Overview](./flows/README.md)
- [Authentication Flow](./flows/authentication-flow.md)
- [Customer CRUD Flow](./flows/customer-flow.md)
- [Request Lifecycle](./flows/request-lifecycle.md)

### Testing
- [Testing Guide](./testing/README.md)
- [Running Tests](./testing/running-tests.md)
- [Writing Tests](./testing/writing-tests.md)

### Deployment
- [Deployment Guide](./deployment/README.md)
- [Production Setup](./deployment/production.md)
- [Troubleshooting](./deployment/troubleshooting.md)

---

## Tech Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | 8.2+ | Runtime |
| Laravel | 12.x | Framework |
| PostgreSQL | 15+ | Database |
| Laravel Sanctum | 4.x | API Authentication |
| Pest | 3.x | Testing Framework |

---

## Quick Reference

### Key Commands

```bash
# Install dependencies
composer install

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Run tests
php artisan test

# Start development server
php artisan serve
```

### API Base URL

```
Development: http://localhost:8000/api/v1
Production:  https://your-domain.com/api/v1
```

### Authentication

All API requests require:
1. **x-api-token** header (API key authentication)
2. **Bearer token** for authenticated endpoints (from login)

---

## Project Contacts

| Role | Name | Email |
|------|------|-------|
| Developer | Aung Zaw Pyae | azp@za.com.mm |

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-01 | Initial release with Auth and Customer modules |

---

*Documentation generated for project handover. Last updated: January 2026*
