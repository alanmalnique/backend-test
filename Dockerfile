FROM php:8.2-fpm

# Instale dependências
RUN apt-get update && apt-get install -y libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

WORKDIR /var/www

COPY . .

RUN composer install

CMD php artisan serve --host=0.0.0.0 --port=8000

EXPOSE 8000
