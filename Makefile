# PHP_CS_FIXER_IGNORE_ENV=1 needed so we can still use PHP 8.4
php-cs-fixer:
	PHP_CS_FIXER_IGNORE_ENV=1 ./vendor/bin/php-cs-fixer check src -v

phpstan:
	./vendor/bin/phpstan analyse src tests

deptrac:
	./vendor/bin/deptrac analyse --report-uncovered --fail-on-uncovered

deptrac-layers:
	./vendor/bin/deptrac debug:layer

test:
	php bin/phpunit
