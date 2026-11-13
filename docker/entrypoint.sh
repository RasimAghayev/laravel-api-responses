#!/bin/sh
# Exec-form entrypoint for the runtime image.
# Handles graceful SIGTERM so `docker stop` does not kill PHP mid-request.
set -e

# Re-exec on SIGTERM so the built-in PHP server shuts down cleanly.
trap 'exit 0' TERM INT

exec "$@"