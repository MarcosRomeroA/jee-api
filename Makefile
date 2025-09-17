dev:
	@docker compose -f compose.yaml -f compose.dev.yaml up -d

build-dev:
	@docker compose -f compose.yaml -f compose.dev.yaml build

build:
	@docker compose -f compose.yaml build

start:
	@docker compose -f compose.yaml up -d

stop:
	@docker compose stop

down:
	@docker compose down

exec:
	@docker compose exec symfony bash

logs:
	@docker compose logs -f

clean-cache:
	@rm -rf var/cache
	@docker compose exec symfony php bin/console cache:clear

migration:
	@docker compose exec symfony php bin/console make:migration

migrate:
	@docker compose exec symfony php bin/console doctrine:migrations:migrate

empty-migration:
	@docker compose exec symfony php bin/console doctrine:migrations:generate

deploy:
	make stop
	make build
	make start
	make clean-cache
