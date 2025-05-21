start:
	docker-compose start

stop:
	docker-compose stop

up:
	docker-compose --env-file backend/.env up -d

down:
	docker-compose down

build:
	docker-compose --env-file backend/.env build

logs:
	docker-compose logs -f

exec:
	docker exec -it CommercePilot-phpfpm bash