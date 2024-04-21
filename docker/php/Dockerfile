FROM php:8.2-fpm-bullseye

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get -y update && apt-get -y upgrade \
    && apt-get -y install sudo git unzip vim supervisor tmux wget gnupg2 libzip-dev \
    && pecl install apcu xdebug \
    && docker-php-ext-install mysqli pdo_mysql opcache zip \
    && docker-php-ext-enable apcu xdebug \
    && touch /tmp/xdebug.log \
    && chmod 777 /tmp/xdebug.log

COPY ./php.ini /usr/local/etc/php/php.ini
COPY ./xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
