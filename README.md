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
- FrankenPHP (worker mode)

## Usage

Start docker:<br>
`docker compose up`

Start local server:<br>
`symfony server:start --no-tls`

Start the player RabbitMQ consumer:<br>
`symfony console messenger:consume async_player`
Create one or multiple worlds:<br>
`make world <worldName>`

Start the chat RabbitMQ consumer:<br>
`symfony console messenger:consume async_message`
Start the RabbitMQ consumers:<br>
`make start`

Start the pending level events RabbitMQ consumer (the one that triggers Mercure updates to clients):<br>
`symfony console messenger:consume async_pending`

Start the scheduler (auto disconnect idle players, prepare level refresh events):<br>
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
