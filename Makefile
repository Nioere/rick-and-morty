PHP := php
COMPOSER := composer
DOCKER_PHP_FPM := docker compose exec php-fpm

up:
	docker compose up -d --remove-orphans

stop:
	docker stop $(shell docker ps -a -q)

delete:
	docker-compose down --remove-orphans

diff:
	$(DOCKER_PHP_FPM) $(PHP) bin/console doctrine:migrations:diff

migrate:
	$(DOCKER_PHP_FPM) $(PHP) bin/console doctrine:migrations:migrate

test-create:
	docker-compose exec php-fpm php bin/console doctrine:database:create --env=test

test-migrate:
	docker-compose exec php-fpm php bin/console doctrine:migrations:migrate --env=test

tests-api:
	docker-compose exec php-fpm sh -c "DATABASE_URL='postgresql://app:password@database:5432/app' ./vendor/bin/codecept run tests/api/"

composer:
	$(DOCKER_PHP_FPM) $(COMPOSER) install