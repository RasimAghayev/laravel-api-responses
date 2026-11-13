# Phase 5 Baseline — laravel-api-responses

Generated 2026-11-13 by the container-engineer (Phase 5). This file is the
behaviour contract for later phases. Everything below was measured inside
Docker — nothing was verified on the host.

## Stack

- PHP 8.5.12 (latest stable as of 2026-11-13)
- Composer 2 (latest stable)
- PHPUnit 13.4.6
- illuminate/http 13.42.0 / illuminate/support 13.42.0
- symfony/http-foundation ^8.0
- Base images pinned by tag AND digest

## Image pins (committed `Dockerfile`)

| Image | Digest |
|---|---|
| composer:2 | sha256:ca11f643d7791ebdd90a34fc20caf11380d434007ae539a9ea75cd9212e13dca |
| php:8.5-cli-alpine | sha256:1ff5b45922ab30f418447c6089dbe2eba5f01e0b2742863d0327ac349305d70f |
| php:8.5-cli | sha256:50956f094e90a716d43bea28087d447555ad80510e386ab8c9c10d8c70d781a9 |

## Build / test results

| Command | Exit | Result |
|---|---|---|
| `docker build --target test .` | 0 | `test` stage builds cleanly |
| `make verify` (= build + test) | 0 | 4 tests / 6 assertions, all pass |
| `make lint` | 0 | `php -l` clean on all response classes |
| `docker compose config` | 0 | compose file validates |
| `make up` | 0 | app container starts |

## Test suite (inside the `test` stage)

```
PHPUnit 13.4.6 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.5.12
Configuration: /app/phpunit.xml

....                                                                4 / 4 (100%)

Time: 00:00.006, Memory: 20.00 MB

Response (Tests\Response)
 ✔  Success api response extends api base response
 ✔  Error api response extends api error response
 ✔  Success api response default code
 ✔  Error api response default code

OK (4 tests, 6 assertions)
```

- Tests: 4, Assertions: 6, Failures: 0, Errors: 0, Warnings: 0
- Coverage: not collected (`--no-coverage`); the suite is tiny and Phase 11 owns expansion.

## API output snapshot

```
$ curl -s http://localhost:20100/health.php
{"status":"ok","service":"laravel-api-responses"}
```
HTTP 200, `Content-Type: application/json`, `X-Powered-By: PHP/8.5.12`.

## Runtime image

- Repository/tag: `laravel-api-responses-p5:local`
- Size: 230 MB
- Non-root user: `app` (verified via `docker exec <c> whoami` -> `app`)
- Health: `docker inspect --format='{{json .State.Health}}' <c>` ->
  `{"Status":"healthy",...}` after the `start-period`
- Extensions loaded: curl, dom, gd, intl, json, libxml, mbstring, pcntl,
  PDO, pdo_sqlite, SimpleXML, sqlite3, xml, xmlreader, xmlwriter, zip
- `STOPSIGNAL SIGTERM`, exec-form `ENTRYPOINT ["/entrypoint.sh"]`,
  graceful `trap 'exit 0' TERM INT`

## Ports / COMPOSE_PROJECT_NAME

- `COMPOSE_PROJECT_NAME` / compose `name`: `laravel-api-responses-p5`
- Reserved port block: `20100-20109` (from `projects/info/state/ports.md`)
- Host mapping: `${APP_HTTP_PORT:-20100}:8080` (container internal 8080)

## Dependent services

None. This is a standalone PHP package library — no DB, cache, queue, or
external service. `docker-compose.yml` therefore has no
`depends_on: condition: service_healthy` to add.

## Secrets statement

- `.env.example` contains placeholders only (`APP_HTTP_PORT`, `APP_ENV`).
- No `.env` is tracked or copied into any image layer.
- No secrets in `ARG`/`ENV`/`COPY`/image layers.
- The library code itself reads zero environment variables
  (grepped `src/` for `getenv` / `$_ENV` / `putenv` / `$_SERVER[` — no matches).

## Known pre-existing issues (NOT fixed here — Phase 5 is Docker/Make only)

1. `composer.json` declares `php: ^8.2` while the pinned illuminate
   packages require `^8.3` and symfony/http-foundation 8.1.x requires
   `>=8.4.1`. The committed image uses PHP 8.5.12, which satisfies all
   runtime constraints; the `^8.2` floor is a Phase 6 upgrade item.
2. No `composer.lock` is committed (intentional for a library published via
   GitHub Packages). Builds are reproducible from `composer.json` only.
3. No CI exists (no `.github/workflows`). Phase 10 owns that.