ifneq (,$(wildcard ./.env))
    include .env
    export
endif

install:
	composer install

migrate:
	php artisan migrate

serve:
	php artisan serve

swagger:
	php artisan l5-swagger:generate

test:
	php artisan test

test-coverage:
	./vendor/bin/phpunit --coverage-text