git pdev:
	@docker compose -f compose.yaml -f compose.override.yaml up -d

build-dev:
	@docker compose -f compose.yaml -f compose.override.yaml build

build:
	@docker compose -f compose.yaml build

start:
	@docker compose -f compose.yaml up -d

stop:
	@docker compose stop

down:
	@docker compose down

exec:
	@docker compose exec symfony sh

logs:
	@docker compose logs -f

clean-cache:
	@rm -rf var/cache
	@docker compose exec symfony php bin/console cache:clear

deploy:
	make stop
	make build
	make start
	make clean-cache
