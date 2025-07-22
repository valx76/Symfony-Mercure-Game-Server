# Symfony-Mercure-Game-Server

**WIP!**

POC of a Habbo's inspired game server, developed using a clean architecture.

## Stack

- PHP 8.4
- Symfony 7.3
- Mercure (for updating data on clients)
- Redis (worlds and players database)
- RabbitMQ (Symfony Messenger's transport)
- Docker

## Usage

Start docker:<br>
`docker compose up`

Start local server:<br>
`symfony server:start --no-tls`

Start the player RabbitMQ consumer:<br>
`symfony console messenger:consume async_player`

Start the chat RabbitMQ consumer:<br>
`symfony console messenger:consume async_message`

Start the scheduler (auto disconnect idle players):<br>
`symfony console messenger:consume scheduler_default`

## Tools

PHP-CS-FIXER:<br>
`make php-cs-fixer`

PHPSTAN / PHPAT (level 10):<br>
`make phpstan`

PHPUNIT:<br>
`make test`

BEHAT:<br>
`make behat`
