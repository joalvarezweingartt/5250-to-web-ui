.PHONY: test setup
setup:
	composer install
test:
	vendor/bin/phpunit
