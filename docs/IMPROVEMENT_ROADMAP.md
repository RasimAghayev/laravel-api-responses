# IMPROVEMENT_ROADMAP.md — laravel-api-responses

Detailed Phase 4–15 plan. Docs-only in Phase 3; no production code changes, no commits, no PRs. Execution requires Gate 10 approval.

## Phase 4 — Git hygiene (partial; Gate 4 candidate)
Tasks:
- Confirm default branch `master`; decide `master` → `main` rename (Gate 4, user approval).
- Leave 2 renovate branches untouched for Phase 6 (per Phase 4 step 3 split).
- Add `.idea/`, `.vscode/` guard to `.gitignore` if artifacts appear (IDE-artifacts decision).
Acceptance: `.gitignore` current; branch decision recorded; no non-bot PRs open.
Critical path: none (blocks nothing downstream).

## Phase 5 — Container baseline
Tasks:
- Add multi-stage `Dockerfile`: `base` (php:8.4-cli pinned digest) → `deps` (composer install with cache mounts) → `test` (runs `vendor/bin/phpunit`; build fails if tests fail) → `runtime` (slim, non-root, HEALTHCHECK, exec-form ENTRYPOINT).
- Add `.dockerignore` (`.git`, `.env*`, keys, vendor, tests artifacts, docs).
- Add `docker-compose.yml` with healthchecks and `COMPOSE_PROJECT_NAME`.
- Add `Makefile`: `build`, `test`, `verify` (= build + test), `up` (depends on verify), `run`, `down`, `lint`, `format`, `security`, `migrate`, `seed`, `clean`.
- Record baseline: build result, test result (4 tests / 6 assertions, exit 0), key API output snapshots in `docs/BASELINE.md`.
Acceptance: `make verify` green in Docker; `make up` starts only after verify.
Critical path: enables Phase 6/10/11 execution in Docker.

## Phase 6 — Upgrade to latest (upgrade order from DEPENDENCY_MAP.md)
Tasks (one at a time, each `make verify` in Docker):
1. `php` ^8.2 → ^8.4 (runtime first).
2. `illuminate/http` 13.33.0 → latest 13.x (13.40.0 at time of audit).
3. `illuminate/support` 13.33.0 → latest 13.x (13.40.0).
4. `symfony/http-foundation` ^8.0 → ^8.1.11.
5. `composer install`; commit `composer.lock`.
6. Add `.github/dependabot.yml` (weekly Monday 06:00 UTC; Composer/npm/Docker/Actions; limits 10/5/5; prefixes `build(deps)`/`build(deps-dev)`/`ci(deps)`).
7. Update `CHANGELOG.md`; no breaking change to this package's public contract (else Gate 5).
Acceptance: all deps at latest stable; `make verify` green; `composer audit` clean or documented.
Critical path: unblocks Phase 7/10/11 (PHP 8.4 required by phpunit 13).

## Phase 7 — Dead code & restructure
Tasks:
- Re-scan for dead code after upgrade (static tooling + tests).
- Narrow `Throwable` catch in `TransactionHelper` to mapped exception types.
- Document `ApiFilter` subclassing requirement / portability of `JSON_CONTAINS`.
Acceptance: no known dead code; `make verify` green; behavior preserved against baseline.
Critical path: none.

## Phase 8 — Architecture
Tasks:
- Record ADR: DDD/EDA/Clean/Hexagonal not applied — small library, KISS/YAGNI win (documented here and in `ARCHITECTURE.md`).
- If P1-4 is addressed: injectable clock/request design decision.
Acceptance: every applied/skipped principle has a one-line reason; `make verify` green.
Critical path: none.

## Phase 9 — Hardening
Tasks:
- Secret-scan pre-commit hook (gitleaks) + CI secret-scan job.
- Dependency audit step in CI (`composer audit` once lockfile committed).
- OWASP Top 10 self-review: response package has no auth/CORS/rate-limiting of its own; document as host-level concerns.
Acceptance: security checklist documented with results; no secrets in code/history.
Critical path: none.

## Phase 10 — CI/CD
Tasks:
- Add GitHub Actions workflow: install+cache → format/lint/secret scan (parallel, first) → tests (Docker `test` stage) → build → dependency security scan.
- `concurrency` with `cancel-in-progress` on PRs; `timeout-minutes` on every job; actions pinned by SHA.
- Target PR pipeline ≤ 10 min wall clock; record actual in `docs/TEST_REPORT.md`.
Acceptance: real CI run green (link in Phase 10 handoff); within time budget.
Critical path: none.

## Phase 11 — Tests
Tasks:
- Add `phpunit.xml` (suite + coverage configuration).
- Expand tests: `ApiFilter` (all operators), `TransactionHelper` (commit/rollBack paths, each exception type → status code), all error response classes (status + envelope shape).
- Test IDs `TC_<MODULE>_<SCENARIO>_<NUMBER>`; results recorded in `docs/TEST_REPORT.md`.
- Regression against `docs/BASELINE.md` (4 tests / 6 assertions).
Acceptance: suite green in Docker and CI; coverage reported honestly.
Critical path: none.

## Phase 15 — Docs, manifest, diagrams
Tasks:
- README improvements (install without real token placeholders).
- `CHANGELOG.md`, `CONTRIBUTING.md`, `SECURITY.md`.
- `repo-manifest.yaml` (type: library).
- Mermaid diagrams from real code (system context, data model).
Acceptance: docs match real implementation; manifest matches dependencies.
Critical path: none.

## Critical path summary
Gate 10 approval → Phase 6 (PHP 8.4 + composer.lock) → Phase 5 container → Phase 10 CI → Phase 11 tests → Phase 15 docs. Phases 4, 7, 8, 9 run in parallel where independent.
