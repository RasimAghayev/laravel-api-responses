# Phase 5 container baseline for laravel-api-responses.
# All heavy work runs inside Docker — nothing is verified on the host.

COMPOSE ?= docker compose
APP   ?= app
TEST  ?= test
PORT  ?= 20100
IMAGE ?= laravel-api-responses-p5:local

.PHONY: build test verify up down run lint shell logs migrate seed clean help

build: ## Build the runtime image (deps -> base -> build -> runtime)
	$(COMPOSE) build $(APP)

test: ## Run the existing test suite inside the Docker `test` stage
	$(COMPOSE) build $(TEST)
	$(COMPOSE) run --rm $(TEST) php vendor/bin/phpunit --no-coverage --testdox

verify: build test ## Verify the baseline: build the runtime image + run the test stage

up: verify ## Start the stack (depends on `verify`)
	$(COMPOSE) up -d $(APP)

down: ## Stop and remove the containers
	$(COMPOSE) down

run: verify ## Run a one-off command in the runtime container
	$(COMPOSE) run --rm $(APP) $(CMD)

lint: ## Run the repo's own linter inside the test stage
	$(COMPOSE) run --rm --entrypoint sh $(TEST) -c "php -l src/Responses/ApiBaseResponse.php && php -l src/Responses/SuccessApiResponse.php && php -l src/Responses/ErrorApiResponse.php"

shell: ## Drop into a shell inside the test stage
	$(COMPOSE) run --rm --entrypoint sh $(TEST)

logs: ## Tail the app container logs
	$(COMPOSE) logs -f $(APP)

migrate: ## No migrations — this is a package library, not an application
	@echo "migrate: not applicable (no database layer in this library)"

seed: ## No seeders — this is a package library, not an application
	@echo "seed: not applicable (no database layer in this library)"

clean: ## Remove build artifacts and containers (never deletes user data — no volumes exist)
	$(COMPOSE) down --rmi local -v
	docker rmi $(IMAGE) 2>/dev/null || true

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'