FROM dunglas/frankenphp:1-php8.4

WORKDIR /app

VOLUME /app/var/

RUN apt-get update && apt-get install -y --no-install-recommends \
	acl \
	file \
	gettext \
	git

RUN set -eux; \
	install-php-extensions \
		@composer \
		apcu \
		intl \
		opcache \
		zip \
        redis \
        amqp

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --link docker/Caddyfile /etc/frankenphp/Caddyfile

ENV APP_ENV=dev
ENV FRANKENPHP_WORKER_CONFIG=watch

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

CMD [ "frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile", "--watch" ]
