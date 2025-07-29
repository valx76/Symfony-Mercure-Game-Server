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

Create one or multiple worlds:<br>
`make world <worldName>`

Start the RabbitMQ consumers:<br>
`make start`

Go to https://localhost to see the POC.<br>
The frontend code is not the subject of this repo so don't mind the cleanliness (it is just to have an overview :).

## Tools

PHP-CS-FIXER:<br>
`make php-cs-fixer`

PHPSTAN / PHPAT (level 10):<br>
`make phpstan`

PHPUNIT:<br>
`make test`

BEHAT:<br>
`make behat`
