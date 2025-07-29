DOCKER_COMP = docker compose
PHP_CONT = $(DOCKER_COMP) exec php


.PHONY: php-cs-fixer
php-cs-fixer:
	@$(PHP_CONT) ./vendor/bin/php-cs-fixer check src -v

.PHONY: phpstan
phpstan:
	@$(PHP_CONT) ./vendor/bin/phpstan analyse src --memory-limit=-1

.PHONY: test
test:
	@$(PHP_CONT) bash -c "APP_ENV=test php bin/phpunit"

.PHONY: behat
behat:
	@$(PHP_CONT) bash -c "APP_ENV=test ./vendor/bin/behat"

.PHONY: start
start:
	@$(PHP_CONT) php bin/console messenger:consume async_player async_message async_pending scheduler_default

.PHONY: world
world:
	@$(PHP_CONT) php bin/console app:generate-world $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))

# Ref: https://stackoverflow.com/questions/6273608/how-to-pass-argument-to-makefile-from-command-line#comment40273073_6273809
%:
	@:
