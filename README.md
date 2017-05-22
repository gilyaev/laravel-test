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

### Via Docker

**Requirements:**

- Docker (https://www.docker.com)
- Docker compose (https://docs.docker.com/compose/)

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

Run application 

```bash
docker-compose up -d
```

Generate application key  and run database migrations with pre-seeded data.

```bash
php artisan key:generate
php artisan migrate --seed
```

Finished visit http://localhost:8080/

## Sample request/response payload for REST API endpoints.

All information can be found here http://docs.laraveltest1.apiary.io

## Testing

```bash
# cd ./test-laravel
# vendor/bin/phpunit tests/
```