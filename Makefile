migrate:
	docker compose exec backend php artisan migrate

migrate-fresh:
	docker compose exec backend php artisan migrate:fresh

up:
	docker compose up -d

down:
	docker compose down

logs:
	docker compose logs -f backend
