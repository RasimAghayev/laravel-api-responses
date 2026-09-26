# Rasim Laravel API Responses

Shared Laravel API response classes extracted from Group B repos (atl_tech, AC-Task, blockchain-monitoring-api).

## Contents

- `ApiBaseResponse` — Abstract base response with standardized JSON envelope (timestamp, path, method, error, result)
- `SuccessApiResponse` — 200-series success responses
- `ApiErrorResponse` — Abstract base for error responses
- `ErrorApiResponse` — Generic 500 error response
- `ErrorInternalServerErrorResponse` — 500 Internal Server Error
- `ErrorMethodNotAllowedResponse` — 405 Method Not Allowed
- `ErrorNotFoundResponse` — 404 Not Found
- `ErrorTokenBlacklisted` — 401 token blacklisted
- `ErrorTokenExpired` — 401 token expired
- `ErrorTokenInvalid` — 401 token invalid
- `ErrorTooManyAttemptsResponse` — 429 Too Many Attempts
- `ErrorUnauthenticatedResponse` — 401 Unauthenticated
- `ErrorValidationResponse` — 422 Validation Error
- `TransactionHelper` — Transaction wrapper with automatic error handling
- `ApiFilter` — Query parameter filter builder

## Installation

```bash
composer require rasim/laravel-api-responses
```

The service provider is auto-discovered by Laravel.

## Usage

```php
use Rasim\LaravelApiResponses\Responses\SuccessApiResponse;

return SuccessApiResponse::make($data, 200);
```
