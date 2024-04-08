
include ./.env

app:
	docker-compose exec app bash
build:
	docker-compose up -d --build
cache:
	docker-compose exec app php artisan view:clear
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
daily:
	docker-compose exec app php artisan app:weather-download --daily
download:
	docker-compose exec app php artisan app:weather-download
import:
	docker-compose exec app php artisan app:weather-import
install:
	docker-compose exec app composer install
migrate:
	docker-compose exec app php artisan migrate
rebuild:
	docker-compose stop
	docker-compose build --no-cache
	docker-compose up -d
restart:
	docker-compose stop
	docker-compose up --build -d
stop:
	docker-compose stop
up:
	docker-compose up -d
web:
	docker-compose exec web /bin/sh
