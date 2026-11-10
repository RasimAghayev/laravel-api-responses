# TECHNICAL_DEBT.md — laravel-api-responses

All debt items classified P0–P3 with remediation mapping. No production code changed in this phase; remediation is scheduled in later phases.

## Debt register

P0:
| ID | Location | Issue | Remediation phase |
|---|---|---|---|
| D-P0-1 | `composer.json:12` | `php: ^8.2` incompatible with phpunit 13 (PHP >=8.4.1) and illuminate 13.40.0 (^8.3) | Phase 6 — raise `php` to `^8.4`, then `composer update` |
| D-P0-2 | `composer.json` / `.gitignore` | `composer.lock` not committed → unreproducible builds | Phase 6 — `composer install` + commit lockfile |
| D-P0-3 | `composer.json:11-14` | exact pins 13.33.0 block patches; 7 versions behind | Phase 6 — `illuminate/http` + `illuminate/support` → `^13.40.0` (or latest) |

P1:
| ID | Location | Issue | Remediation phase |
|---|---|---|---|
| D-P1-1 | repository root | no CI/CD workflow | Phase 10 — GitHub Actions: validate + outdated + phpunit |
| D-P1-2 | repository root | no Dockerfile / compose / Makefile | Phase 5 — multi-stage Dockerfile + Makefile |
| D-P1-3 | `src/Support/TransactionHelper.php:36` | broad `Throwable` catch; generic 500 | Phase 7 — narrow catch blocks, typed error mapping |
| D-P1-4 | `src/Responses/ApiBaseResponse.php:31-58` | `now()`/`request()` need booted Laravel app | Phase 8 — consider injectable clock/request or document app-level requirement |

P2:
| ID | Location | Issue | Remediation phase |
|---|---|---|---|
| D-P2-1 | `src/Support/Filters/ApiFilter.php` | empty defaults; MySQL-specific `JSON_CONTAINS` | Phase 7/8 — document portability, require subclass config |
| D-P2-2 | `tests/ResponseTest.php` | only 4 tests; narrow coverage | Phase 11 — add tests for ApiFilter, TransactionHelper, errors |
| D-P2-3 | repository root | no `phpunit.xml` | Phase 11 — add phpunit.xml with suite + coverage |

P3:
| ID | Location | Issue | Remediation phase |
|---|---|---|---|
| D-P3-1 | `README.md` | references S1 spec not in repo; token placeholder pattern | Phase 15 — README cleanup |
| D-P3-2 | repository root | no `CHANGELOG.md` / `CONTRIBUTING.md` / `SECURITY.md` | Phase 15 — add standard docs |

## Remediation roadmap mapping
| Debt ID | Phase | Acceptance criteria |
|---|---|---|
| D-P0-1, D-P0-2, D-P0-3 | 6 | `composer validate` clean (no pin warnings); `composer.lock` committed; tests green on PHP 8.4 |
| D-P1-1 | 10 | CI pipeline green on PR, ≤10 min wall clock |
| D-P1-2 | 5 | `make verify` builds image and runs test stage green |
| D-P1-3 | 7 | `make verify` green; exception mapping preserves status codes |
| D-P1-4 | 8 | ADR written; behavior unchanged (`make verify` green) |
| D-P2-1 | 7/8 | whitelist populated or subclassing documented |
| D-P2-2, D-P2-3 | 11 | `phpunit.xml` present; suite green with expanded coverage |
| D-P3-1, D-P3-2 | 15 | README/CHANGELOG/CONTRIBUTING/SECURITY present |

## Burndown targets
- P0: resolved before any code-writing phase (Phase 6 unblocks Phases 5/7/10/11).
- P1: resolved by end of Phase 11 (CI green, container green).
- P2: resolved by end of Phase 11 (tests) / Phase 8 (architecture ADR).
- P3: resolved by end of Phase 15 (docs).

## Exceptions (documented per standing decision 5)
None active; all P0 items have a clear upgrade path. If PHP 8.4 is unavailable in a target host environment, raise a written blocker here and use the highest compatible PHP version meanwhile.
