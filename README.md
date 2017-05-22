# Laravel

**Requirements:**

- Mysql >= 5.6
- Composer
- PHP >= 5.6.4
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## Installation

### General set up steps

Get source code

```bash
# git clone git@github.com:gilyaev/laravel-test.git ./test-laravel
# cd ./test-laravel
# cp .env.example .env
```

Change database setting (.env file)

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=33061
DB_DATABASE=shop
DB_USERNAME=root
DB_PASSWORD=secret
```

Install application dependencies 

```bash
composer install
```

### Run via Docker

**Requirements:**

- Docker (https://www.docker.com)
- Docker compose (https://docs.docker.com/compose/)

Run application 

```bash
docker-compose up -d
```

### Run without Docker

See https://laravel.com/docs/5.4/installation

## Sample request/response payload for REST API endpoints.

All information can be found here http://docs.laraveltest1.apiary.io

## Testing

```bash
# cd ./test-laravel
# vendor/bin/phpunit tests/
```