
## Assignment


### About
This (Laravel) project was created based on a assignment for a job interview.

### Setup

1: Copy `.env.example` to `.env` and fill in `DB_*` properties to the `.env` file

2: Install libraries
```
composer install
```

3: Run migrations

```
php artisan migrate
```

4: Seed database

```
php artisan db:seed
```

### Run code

```
php artisan serve
```

This project wil be served on http://127.0.0.0.1 by default



### Run tests

Tests are run with phpunit

```
vendor/phpunit/phpunit/phpunit
``` 
