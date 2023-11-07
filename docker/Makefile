
include ./.env

app:
	docker-compose exec app bash
build:
	docker-compose up -d --build
cache:
	docker-compose exec app php artisan view:clear
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
migrate:
	docker-compose exec app php artisan migrate
	#docker-compose exec app /usr/local/bin/php artisan migrate
restart:
	docker-compose stop
	docker-compose up -d
stop:
	docker-compose stop
up:
	docker-compose up -d
web:
	docker-compose exec web /bin/sh
