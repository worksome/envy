FROM php:8.2-cli-alpine

RUN apk add --no-cache $PHPIZE_DEPS linux-headers
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

ENTRYPOINT ["php"]
