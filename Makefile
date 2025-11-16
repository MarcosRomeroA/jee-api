.PHONY: dev build-dev build start stop down exec logs clean-cache migration migrate test behat unit test-player

dev:
	@docker compose -f compose.yaml -f compose.dev.yaml up -d

build-dev:
	@docker compose -f compose.yaml -f compose.dev.yaml build --no-cache

build:
	@docker compose -f compose.yaml build --no-cache

start:
	@docker compose -f compose.yaml up -d

stop:
	@docker compose -f compose.yaml -f compose.dev.yaml stop

down:
	@docker compose -f compose.yaml -f compose.dev.yaml down

exec:
	@docker exec -it jee_symfony bash

logs:
	@docker compose -f compose.yaml -f compose.dev.yaml logs -f symfony

logs-test:
	@docker exec jee_symfony tail -100 var/log/test.log

clean-cache:
	@docker exec jee_symfony php -d memory_limit=128M bin/console cache:clear --no-warmup
	@docker exec jee_symfony php -d memory_limit=128M bin/console cache:clear --env=test --no-warmup

migration-diff:
	@docker exec jee_symfony php bin/console doctrine:migrations:diff

migrate:
	@docker exec jee_symfony php bin/console doctrine:migrations:migrate -n

migrate-test:
	@docker exec jee_symfony php bin/console doctrine:migrations:migrate -n --env=test

routes:
	@docker exec jee_symfony php bin/console debug:router

routes-player:
	@docker exec jee_symfony php bin/console debug:router | grep player

behat: ## Run Behat tests
	@echo "🧪 Running Behat tests..."
ifdef tag
	@docker exec jee_symfony vendor/bin/behat --tags=@$(tag)
else ifdef ARGS
	@docker exec jee_symfony vendor/bin/behat $(ARGS)
else
	@docker exec jee_symfony vendor/bin/behat
endif

unit: ## Run PHPUnit tests
	@echo "🧪 Running PHPUnit tests..."
	@docker exec jee_symfony vendor/bin/phpunit

test: behat unit ## Run all tests (Behat + PHPUnit)
	@echo "✅ All tests completed!"

reset-test-db: ## Reset test database completely
	@echo "🔄 Resetting test database..."
	@docker exec jee_symfony php bin/console doctrine:schema:drop --force --env=test
	@docker exec jee_symfony php bin/console doctrine:schema:create --env=test
	@docker exec jee_symfony php bin/console doctrine:migrations:migrate --no-interaction --env=test
	@docker exec jee_symfony rm -rf var/cache/test/*
	@docker exec jee_symfony php bin/console cache:clear --env=test
	@echo "✅ Test database ready!"

test-clean: reset-test-db test ## Reset DB and run all tests

test-player:
	@docker exec jee_symfony vendor/bin/behat tests/Behat/Web/Player/ --format=pretty

test-verbose:
	@docker exec jee_symfony vendor/bin/behat tests/Behat/Web/Player/ --format=pretty -vv

setup:
	@./setup-and-test.sh

help:
	@echo "Comandos disponibles:"
	@echo "  make dev              - Iniciar contenedores en modo desarrollo"
	@echo "  make build-dev        - Reconstruir imagen de desarrollo"
	@echo "  make stop             - Detener contenedores"
	@echo "  make down             - Detener y eliminar contenedores"
	@echo "  make exec             - Entrar al contenedor de Symfony"
	@echo "  make logs             - Ver logs en tiempo real"
	@echo "  make logs-test        - Ver logs de tests"
	@echo "  make clean-cache      - Limpiar cache de Symfony"
	@echo "  make migration-diff   - Generar nueva migración"
	@echo "  make migrate          - Ejecutar migraciones (dev)"
	@echo "  make migrate-test     - Ejecutar migraciones (test)"
	@echo "  make routes           - Ver todas las rutas"
	@echo "  make routes-player    - Ver rutas de Player"
	@echo "  make behat            - Ejecutar tests de Behat"
	@echo "  make behat tag=<tag>  - Ejecutar tests con un tag específico (ej: make behat tag=mercure)"
	@echo "  make unit             - Ejecutar tests unitarios (PHPUnit)"
	@echo "  make test             - Ejecutar todos los tests (Behat + PHPUnit)"
	@echo "  make test-player      - Ejecutar tests de Player"
	@echo "  make test-verbose     - Ejecutar tests con verbose"
	@echo "  make setup            - Setup completo + tests"
	@echo "  make help             - Mostrar esta ayuda"

migrate:
	@docker compose exec symfony php bin/console doctrine:migrations:migrate

empty-migration:
	@docker compose exec symfony php bin/console doctrine:migrations:generate

migration:
	@docker compose exec symfony php bin/console make:migration

deploy:
	make stop
	make build
	make start
	make clean-cache
