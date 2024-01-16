# URL Shortener

Simple URL Shortener using Workerman, SQLite PDO, DotEnv, Validator and Twig.


## Requirements

- PHP >= 8.0 with pcntl, sqlite and posix extension

## Installation

- git clone
- cd project dir
- run `cp .env.example .env`
- run `php server.php start`

## Available commands

### Start

Run as debug mode

`php server.php start`

Run as daemon mode

`php server.php start -d`

Stop

`php server.php stop`

Restart

`php server.php restart`

Graceful restart

`php server.php reload`

Status

`php server.php status`