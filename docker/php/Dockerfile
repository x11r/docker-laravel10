FROM php:8.1-fpm-bullseye
#FROM php:8.0.16-fpm-buster

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get -y update \
    && apt-get -y upgrade \
    && apt-get -y install git unzip vim supervisor \
    && pecl install apcu xdebug \
    && docker-php-ext-install mysqli pdo_mysql opcache \
    && docker-php-ext-enable apcu xdebug
