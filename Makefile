php-cs-fixer:
	./vendor/bin/php-cs-fixer check src -v

phpstan:
	./vendor/bin/phpstan analyse src tests

deptrac:
	./vendor/bin/deptrac analyse --report-uncovered --fail-on-uncovered

deptrac-layers:
	./vendor/bin/deptrac debug:layer

test:
	php bin/phpunit

behat:
	./vendor/bin/behat
