build:
	docker compose build
	make up

up:
	docker compose up -d
	@echo "App is running at http://127.0.0.1:8000"

stop:
	docker compose stop

exec:
	docker compose exec php sh

test:
	php vendor/bin/behat
