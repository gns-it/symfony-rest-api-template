init: dd dp db du island-init
up: du
down: dd
restart: dd du

cli:
	docker-compose exec php bash

du:
	docker-compose up -d

dd:
	docker-compose down --remove-orphans

dp:
	docker-compose pull

db:
	docker-compose build

island-init:
	git pull origin develop
	docker-compose exec php composer install
