php-cs-fixer:
	./vendor/bin/php-cs-fixer check src -v

phpstan:
	./vendor/bin/phpstan analyse src --memory-limit=-1

test:
	php bin/phpunit

behat:
	./vendor/bin/behat
