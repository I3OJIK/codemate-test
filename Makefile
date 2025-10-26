env:
	cp .env.example .env
build:
	docker compose build
install:
	docker compose exec php-fpm composer install -o
up:
	docker compose up -d
down:
	docker compose down --remove-orphans
migrate:
	docker compose exec php-fpm php artisan migrate --seed
test:
	docker compose exec php ./vendor/bin/phpunit

setup:
	$(MAKE) env;
	$(MAKE) build;
	$(MAKE) up;
	$(MAKE) install;
	$(MAKE) migrate;
	@echo "Setup complete"