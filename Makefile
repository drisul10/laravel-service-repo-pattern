ifneq (,$(wildcard ./.env))
    include .env
    export
endif

generate-swagger:
	php artisan l5-swagger:generate

install:
	composer install

serve:
	php artisan serve

test:
	php artisan test

test-coverage:
	./vendor/bin/phpunit --coverage-text