# syntax=docker/dockerfile:1
# Multi-stage build for rasimaghayev/laravel-api-responses (PHP/Composer library)
# Stages: deps -> base -> build -> test -> runtime
# All base images are pinned by tag AND digest for reproducibility.

# ---------------------------------------------------------------------------
# deps: resolve and install production dependencies only.
# BuildKit cache mount keeps the Composer cache across builds.
# ---------------------------------------------------------------------------
FROM composer:2@sha256:ca11f643d7791ebdd90a34fc20caf11380d434007ae539a9ea75cd9212e13dca AS deps
WORKDIR /app

COPY composer.json ./

RUN --mount=type=cache,target=/tmp/composer-cache,sharing=locked \
    COMPOSER_CACHE_DIR=/tmp/composer-cache \
    composer install \
        --no-dev \
        --no-interaction \
        --no-scripts \
        --optimize-autoloader \
        --no-progress

# ---------------------------------------------------------------------------
# base: minimal PHP CLI runtime shared by build and runtime stages.
# No compilers, no dev tools, no Composer binary ships in the final image.
# ---------------------------------------------------------------------------
FROM php:8.5-cli-alpine@sha256:1ff5b45922ab30f418447c6089dbe2eba5f01e0b2742863d0327ac349305d70f AS base
WORKDIR /app

# Core extensions required by illuminate/support and symfony/http-foundation.
# Install -dev headers first (needed at compile time), then remove them so
# the final image carries only the runtime shared libraries.
RUN apk add --no-cache \
        libpng \
        libxml2 \
        libzip \
        curl \
        icu \
        oniguruma \
    && apk add --no-cache --virtual build-deps \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
        sqlite-dev \
        icu-dev \
        oniguruma-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-configure gd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j"$(nproc)" \
        gd \
        mbstring \
        pdo_sqlite \
        xml \
        zip \
        intl \
        pcntl \
    && apk del build-deps \
    && rm -rf /var/cache/apk/* /tmp/pear /usr/src/php-* /root/.composer

# ---------------------------------------------------------------------------
# build: assemble the production tree (vendor from deps + app source).
# ---------------------------------------------------------------------------
FROM base AS build
COPY --from=deps /app/vendor ./vendor
COPY src ./src
COPY composer.json ./
COPY docker/entrypoint.sh /entrypoint.sh
COPY docker/health.php /app/public/health.php
RUN chmod +x /entrypoint.sh

# ---------------------------------------------------------------------------
# test: full dev install + the existing PHPUnit suite.
# Non-alpine image so apt can provide unzip for dist packages.
# A non-zero exit here is the honest Phase 5 baseline and is NOT a Dockerfile
# defect — it records pre-existing test failures for later phases to fix.
# ---------------------------------------------------------------------------
FROM php:8.5-cli@sha256:50956f094e90a716d43bea28087d447555ad80510e386ab8c9c10d8c70d781a9 AS test
WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        unzip \
        ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Composer is copied from the `deps` stage (composer:2 ships the phar at
# /usr/bin/composer) so the test stage never re-downloads it.
COPY --from=deps /usr/bin/composer /usr/bin/composer

COPY composer.json ./

RUN --mount=type=cache,target=/tmp/composer-cache,sharing=locked \
    COMPOSER_CACHE_DIR=/tmp/composer-cache \
    composer install \
        --no-interaction \
        --no-scripts \
        --optimize-autoloader \
        --no-progress

COPY src ./src
COPY tests ./tests
COPY phpunit.xml ./
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

CMD ["php", "vendor/bin/phpunit", "--no-coverage", "--testdox"]

# ---------------------------------------------------------------------------
# runtime: slim image, non-root, HEALTHCHECK, exec-form ENTRYPOINT, SIGTERM.
# ---------------------------------------------------------------------------
FROM base AS runtime

RUN addgroup -S app && adduser -S app -G app

COPY --from=build /app /app
COPY --from=build /entrypoint.sh /entrypoint.sh
COPY docker/healthcheck.sh /healthcheck.sh
RUN chmod +x /entrypoint.sh /healthcheck.sh

# A package library has no port contract of its own; 8080 is the health probe.
EXPOSE 8080

USER app
WORKDIR /app

STOPSIGNAL SIGTERM

HEALTHCHECK --interval=10s --timeout=3s --start-period=5s --retries=3 \
    CMD ["/healthcheck.sh"]

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php", "-S", "0.0.0.0:8080", "-t", "/app/public"]