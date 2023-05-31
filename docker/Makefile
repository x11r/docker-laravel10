
include ./.env

app:
	docker-compose exec app bash
build:
	docker-compose up -d --build
migrate:
	docker-compose exec app /usr/local/bin/php artisan migrate
stop:
	docker-compose stop
up:
	docker-compose up -d
