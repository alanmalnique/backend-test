# Backend Project

## Requisites to run the project
- PHP 8+
- Composer 2
- PHPUnit

## How to run
- Copy the .env.example file: `cp .env.example .env`
- Modify the database settings, both the DB_ and DB_TEST_
- Run `composer install`
- Run `php artisan key:generate`
- To run the server, run `php artisan serve`
- The server will be available at `http://localhost:8000`

## How to run the tests
- Run `./vendor/bin/phpunit`
- To run and print the coverage in html, run `./vendor/bin/phpunit --coverage-html coverage-report`
- The report will be available at `./coverage-report`. You have to open the file `index.html`

## Available endpoints
- `GET /api/customers/{id}` - Get customer details from ID
- `POST /api/customers` - Create a new customer
- `PUT /api/customers/{id}` - Update existing customer data
- `DELETE /api/customer/{id}` - Remove existing customer
- `GET /api/accounts/{id}` - Get balance from existing customer
- `POST /api/accounts/{id}/deposit` - Make a deposit for an existing customer
- `POST /api/accounts/{id}/withdraw` - Make a withdrawal for an existing customer
- `POST /api/accounts/transfer` - Make a transfer between two existing customers
