FROM php:8.4-cli-alpine

RUN apk add --no-cache sqlite sqlite-dev unzip git \
    && docker-php-ext-install pdo pdo_sqlite bcmath

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]