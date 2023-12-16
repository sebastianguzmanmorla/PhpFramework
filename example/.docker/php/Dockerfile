FROM php:8.3-fpm
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git zlib1g-dev libzip-dev libpq-dev libpng-dev unzip

ARG APP_ENV

RUN echo $APP_ENV

RUN docker-php-ext-install pgsql mysqli bcmath gd zip \
    && docker-php-ext-enable pgsql mysqli bcmath gd zip

RUN if [ "$APP_ENV" = "local" ]; then \
    pecl install xdebug \
	&& docker-php-ext-enable xdebug; fi