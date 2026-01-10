# API Documentation

## Overview

This is a RESTful API built with Laravel, providing JSON-based endpoints for authentication and customer management.

## Base URL

```
Development: http://localhost:8000/api/v1
Production:  https://your-domain.com/api/v1
```

## API Versioning

The API uses URL-based versioning. Current version: `v1`

```
/api/v1/login
/api/v1/customers
```

---

## Authentication

### API Token Authentication

All requests must include the API token header:

```http
x-api-token: your-api-token-here
```

Configure in `.env`:
```env
API_TOKEN=your-secure-api-token
API_TOKEN_HEADER=x-api-token
```

### Bearer Token Authentication

Protected endpoints require a Bearer token obtained from login:

```http
Authorization: Bearer {access_token}
```

---

## Request Format

### Headers

| Header | Required | Description |
|--------|----------|-------------|
| `Content-Type` | Yes | `application/json` |
| `Accept` | Yes | `application/json` |
| `x-api-token` | Yes | API authentication token |
| `Authorization` | For protected | `Bearer {token}` |

### Example Request

```bash
curl -X POST https://api.example.com/api/v1/customers \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "x-api-token: your-api-token" \
  -H "Authorization: Bearer your-bearer-token" \
  -d '{"name": "John Doe", "email": "john@example.com"}'
```

---

## Response Format

### Success Response

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data
  }
}
```

### Paginated Response

```json
{
  "success": true,
  "message": "Success",
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150,
    "from": 1,
    "to": 15
  },
  "links": {
    "first": "...?page=1",
    "last": "...?page=10",
    "prev": null,
    "next": "...?page=2"
  }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error description",
  "error_code": "ERROR_CODE",
  "errors": {
    "field": ["Validation message"]
  }
}
```

---

## HTTP Status Codes

| Code | Meaning | Usage |
|------|---------|-------|
| `200` | OK | Successful GET, PUT |
| `201` | Created | Successful POST |
| `204` | No Content | Successful DELETE |
| `400` | Bad Request | Invalid request |
| `401` | Unauthorized | Missing/invalid auth |
| `403` | Forbidden | Not permitted |
| `404` | Not Found | Resource not found |
| `422` | Unprocessable | Validation failed |
| `500` | Server Error | Internal error |

---

## Rate Limiting

| Endpoint Type | Limit | Window |
|---------------|-------|--------|
| General API | 60 requests | 1 minute |
| Authentication | 10 requests | 1 minute |
| Sensitive | 5 requests | 1 minute |

Rate limit headers:
```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
```

---

## API Endpoints

### Authentication
- [POST /login](./authentication.md#login) - User login
- [POST /logout](./authentication.md#logout) - User logout
- [POST /logout-all](./authentication.md#logout-all) - Logout all devices
- [GET /me](./authentication.md#me) - Get current user

### Customers
- [GET /customers](./customers.md#list-customers) - List customers
- [POST /customers](./customers.md#create-customer) - Create customer
- [GET /customers/{uuid}](./customers.md#get-customer) - Get customer
- [PUT /customers/{uuid}](./customers.md#update-customer) - Update customer
- [DELETE /customers/{uuid}](./customers.md#delete-customer) - Delete customer
- [POST /customers/{uuid}/restore](./customers.md#restore-customer) - Restore customer

---

## Error Codes

| Code | Description |
|------|-------------|
| `INVALID_API_TOKEN` | API token missing or invalid |
| `INVALID_CREDENTIALS` | Login credentials incorrect |
| `USER_NOT_FOUND` | User does not exist |
| `CUSTOMER_NOT_FOUND` | Customer does not exist |
| `CUSTOMER_ALREADY_EXISTS` | Email already registered |
| `VALIDATION_ERROR` | Request validation failed |
| `UNAUTHORIZED` | Authentication required |
| `FORBIDDEN` | Permission denied |
| `NOT_FOUND` | Resource not found |
| `SERVER_ERROR` | Internal server error |
