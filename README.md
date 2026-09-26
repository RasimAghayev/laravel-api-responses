# RasimAghayev Laravel API Responses

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

This package is published via **GitHub Packages (ghcr.io)**, not Packagist.
The `rasimaghayev` vendor namespace on Packagist.org is already claimed by another
party; see the S1 spec (repo-consolidation §3.2/§4.2) for registry rationale.

```bash
# 1. Configure Composer to use ghcr.io
composer config repositories.rasimaghayev/laravel-api-responses \
  composer https://github.com/RasimAghayev/laravel-api-responses

# 2. Authenticate with GitHub Packages
export GITHUB_TOKEN=ghp_xxx  # or: git config --global \
  http.extraheader "EXT project_github_token: $GITHUB_TOKEN"

# 3. Install the package
composer require rasimaghayev/laravel-api-responses
```

The service provider is auto-discovered by Laravel.

## Usage

```php
use RasimAghayev\LaravelApiResponses\Responses\SuccessApiResponse;

return SuccessApiResponse::make($data, 200);
```
