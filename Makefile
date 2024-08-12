start:
	docker compose -f compose.yaml up -d --build

stop:
	docker compose -f compose.yaml stop

down:
	docker compose -f compose.yaml down

exec:
	docker compose exec symfony sh

logs:
	docker compose logs -f

cc:
	docker compose exec symfony php bin/console cache:clear