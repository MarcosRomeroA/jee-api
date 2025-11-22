.PHONY: dev build-dev build start stop down exec logs clean-cache migration migrate test behat unit test-player messenger-retry messenger-stats messenger-consume deploy deploy-symfony

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

clean-cache-test:
	@docker exec jee_symfony php -d memory_limit=128M bin/console doctrine:database:create --if-not-exists --env=test || true
	@docker exec jee_symfony php -d memory_limit=128M bin/console cache:clear --env=test --no-warmup

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

migration:
	@docker exec jee_symfony php bin/console make:migration

migrate:
	@docker exec jee_symfony php bin/console doctrine:migrations:migrate -n

migrate-test:
	@docker exec jee_symfony php bin/console doctrine:migrations:migrate -n --env=test

migration-diff:
	@docker exec jee_symfony php bin/console doctrine:migrations:diff

empty-migration:
	@docker exec jee_symfony php bin/console doctrine:migrations:generate

deploy:
	make stop
	make build
	make start
	make clean-cache

deploy-symfony: ## Deploy solo el contenedor symfony en producción (usar con sudo)
	@echo "🚀 Deploying symfony container..."
	@docker compose -f compose.yaml stop symfony
	@docker compose -f compose.yaml build --no-cache symfony
	@docker compose -f compose.yaml up -d symfony
	@echo "✅ Symfony deployed successfully!"

messenger-stats: ## Ver estadísticas de colas de RabbitMQ
	@echo "📊 RabbitMQ Queue Statistics"
	@echo "============================"
	@docker compose exec -T rabbitmq rabbitmqctl list_queues name messages messages_ready messages_unacknowledged

messenger-retry-low: ## Reintentar todos los mensajes fallidos de low_priority
	@echo "🔄 Retrying all failed messages from low_priority queue..."
	@docker compose exec -T symfony php bin/console messenger:stop-workers
	@sleep 2
	@docker compose exec -T symfony php bin/console messenger:consume failed_low_priority --limit=100 --time-limit=60 -vv
	@docker compose exec -T symfony supervisorctl restart messenger-consume-low-priority:messenger-consume-low-priority_00
	@echo "✅ Failed messages processed. Worker restarted."

messenger-retry-high: ## Reintentar todos los mensajes fallidos de high_priority
	@echo "🔄 Retrying all failed messages from high_priority queue..."
	@docker compose exec -T symfony php bin/console messenger:stop-workers
	@sleep 2
	@docker compose exec -T symfony php bin/console messenger:consume failed_high_priority --limit=100 --time-limit=60 -vv
	@docker compose exec -T symfony supervisorctl restart messenger-consume-high-priority:messenger-consume-high-priority_00
	@echo "✅ Failed messages processed. Worker restarted."

messenger-consume-low: ## Consumir manualmente mensajes de low_priority (útil para debugging)
	@docker compose exec -T symfony php bin/console messenger:consume low_priority -vv

messenger-consume-high: ## Consumir manualmente mensajes de high_priority (útil para debugging)
	@docker compose exec -T symfony php bin/console messenger:consume high_priority -vv

messenger-worker-status: ## Ver estado de los workers de Supervisor
	@docker compose exec -T symfony supervisorctl status

messenger-worker-restart: ## Reiniciar todos los workers de Messenger
	@echo "🔄 Restarting all Messenger workers..."
	@docker compose exec -T symfony supervisorctl restart all
	@echo "✅ All workers restarted."

messenger-worker-logs: ## Ver logs de los workers
	@echo "📋 Low Priority Worker Logs:"
	@echo "============================"
	@docker compose exec -T symfony tail -50 /var/www/html/var/log/messenger_low_priority.log
	@echo ""
	@echo "📋 Low Priority Error Logs:"
	@echo "============================"
	@docker compose exec -T symfony tail -50 /var/www/html/var/log/messenger_low_priority_error.log

help:
	@echo "Comandos disponibles:"
	@echo ""
	@echo "Docker & App:"
	@echo "  make dev              - Iniciar contenedores en modo desarrollo"
	@echo "  make build-dev        - Reconstruir imagen de desarrollo"
	@echo "  make stop             - Detener contenedores"
	@echo "  make down             - Detener y eliminar contenedores"
	@echo "  make exec             - Entrar al contenedor de Symfony"
	@echo "  make logs             - Ver logs en tiempo real"
	@echo "  make clean-cache      - Limpiar cache de Symfony"
	@echo "  make deploy-symfony   - Deploy solo symfony en producción (sudo)"
	@echo ""
	@echo "Database:"
	@echo "  make migration-diff   - Generar nueva migración"
	@echo "  make migrate          - Ejecutar migraciones (dev)"
	@echo "  make migrate-test     - Ejecutar migraciones (test)"
	@echo ""
	@echo "Tests:"
	@echo "  make behat            - Ejecutar tests de Behat"
	@echo "  make behat tag=<tag>  - Ejecutar tests con un tag específico"
	@echo "  make unit             - Ejecutar tests unitarios (PHPUnit)"
	@echo "  make test             - Ejecutar todos los tests"
	@echo ""
	@echo "Messenger & RabbitMQ:"
	@echo "  make messenger-stats           - Ver estadísticas de colas"
	@echo "  make messenger-retry-low       - Reintentar mensajes fallidos (low priority)"
	@echo "  make messenger-retry-high      - Reintentar mensajes fallidos (high priority)"
	@echo "  make messenger-worker-status   - Ver estado de workers"
	@echo "  make messenger-worker-restart  - Reiniciar todos los workers"
	@echo "  make messenger-worker-logs     - Ver logs de workers"
	@echo "  make messenger-consume-low     - Consumir manualmente (debugging)"
	@echo ""
	@echo "  make help             - Mostrar esta ayuda"
