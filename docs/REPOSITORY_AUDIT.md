# REPOSITORY_AUDIT.md — laravel-api-responses

## Full file inventory
| Path | Role |
|---|---|
| `composer.json` | manifest (php ^8.2, illuminate 13.33.0 exact pins, symfony/http-foundation ^8.0, phpunit ^13.0 dev) |
| `composer.lock` | NOT committed (gitignored) — unreproducible builds |
| `LICENSE` | MIT |
| `README.md` | usage + install via GitHub Packages |
| `.gitignore` | `vendor/`, `node_modules/`, `composer.lock`, `phpunit.result.cache` |
| `.gitattributes` | `text=auto`, LF for php/md/json/yml/yaml |
| `src/LaravelApiResponsesServiceProvider.php` | auto-discovered, empty register/boot |
| `src/Responses/ApiBaseResponse.php` | abstract base, Responsable, JSON envelope builder |
| `src/Responses/SuccessApiResponse.php` | default 200 |
| `src/Responses/ApiErrorResponse.php` | abstract error base |
| `src/Responses/ErrorApiResponse.php` | 500 |
| `src/Responses/ErrorInternalServerErrorResponse.php` | 500 |
| `src/Responses/ErrorMethodNotAllowedResponse.php` | 405 |
| `src/Responses/ErrorNotFoundResponse.php` | 404 |
| `src/Responses/ErrorTokenBlacklisted.php` | 401 |
| `src/Responses/ErrorTokenExpired.php` | 401 |
| `src/Responses/ErrorTokenInvalid.php` | 401 |
| `src/Responses/ErrorTooManyAttemptsResponse.php` | 429 |
| `src/Responses/ErrorUnauthenticatedResponse.php` | 401 |
| `src/Responses/ErrorValidationResponse.php` | 422 |
| `src/Support/TransactionHelper.php` | DB transaction wrapper |
| `src/Support/Filters/ApiFilter.php` | query-param filter builder |
| `tests/ResponseTest.php` | 4 PHPUnit tests |
| `Dockerfile*` | none |
| `docker-compose*` | none |
| CI workflows | none |
| `phpunit.xml` | none |
| `.env.example` | none |

## Dependency analysis
- `composer validate` → exit 0; warnings: exact version constraints on `illuminate/http` and `illuminate/support`; root package version defaults to `1.0.0` (no git tag).
- `composer outdated --direct` → exit 0; `illuminate/http` 13.33.0 → 13.40.0 available; `illuminate/support` 13.33.0 → 13.40.0 available.
- `composer.lock` absent from repo → transitive dependencies unknown; `composer audit` cannot run.
- Cross-repo source: extracted from atl_tech, AC-Task, blockchain-monitoring-api (per README). Published via ghcr.io; installation requires `GITHUB_TOKEN`.

## Config review
Service provider is empty and auto-discovered. No config file, no env vars. `.gitignore` correctly excludes `vendor/` and `composer.lock`. `.gitattributes` enforces LF line endings.

## Code quality findings
- `declare(strict_types=1);` on all files; PSR-4 autoload `RasimAghayev\LaravelApiResponses\ → src/`.
- `ApiBaseResponse::formatResult()` handles `JsonResponse`, objects with `toArray()`, objects with `getData()`, and arrays with `original` key.
- `ApiBaseResponse::toResponse()` depends on Laravel helpers `now()` and `request()` — unusable outside a booted app.
- `ApiErrorResponse::toResponse()` re-processes parent output; result is moved into `error`.
- `TransactionHelper` catches `Throwable` broadly (line 36) and maps all unexpected errors to generic 500.
- `ApiFilter` safe-parm whitelist; `LIKE`/`ILIKE` append `%`; `JSON_CONTAINS` is MySQL-specific.
- Tests: 4 tests / 6 assertions, all pass on PHP 8.4 (exit 0); fail to even load on PHP 8.2 (phpunit requires >=8.4.1).

## Security scan
Full-history scan (`git log --all --oneline`, 13 commits): clean — no secrets, tokens, keys, or `APP_KEY` values. No `.env` committed. Package handles no credentials itself; token error classes are DTOs only.

## CI/CD status
None. No GitHub Actions, GitLab CI, or other pipeline. No Dependabot config. No automated testing on PRs.

## Git hygiene
- Default branch: `master`.
- Remote branches: `master`, `renovate/illuminate-http-13.x`, `renovate/illuminate-support-13.x`.
- 13 commits; conventional-commit style (`chore(deps):`, `docs:`, `refactor:`).
- No open non-bot PRs known (local mirror). No `.idea`/`.vscode` artifacts present.
