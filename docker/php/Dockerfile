FROM php:8.2-fpm-bullseye

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#RUN apt-get -y update \
#    && apt-get -y upgrade \
#    && apt-get -y install git unzip vim supervisor tmux wget gnupg2 \
#	&& pecl install apcu xdebug \
#    && docker-php-ext-install mysqli pdo_mysql opcache \
#    && docker-php-ext-enable apcu xdebug \
#    && mkdir -p /var/www/storage/framework/cache/data \
#    && mkdir -p /var/www/storage/framework/sessions \
#    && mkdir -p /var/www/storage/framework/views \
#    && mkdir -p /var/www/storage/framework/testing \
#    && chmod -R 777 /var/www/storage


#RUN apt-get -y update && apt-get -y upgrade && apt-get install -y git unzip vim supervisor tmux wget gnupg2 \
#    && wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | gpg --dearmour -o /usr/share/keyrings/google-keyring.gpg \
#    && sh -c 'echo "deb [arch=amd64 signed-by=/usr/share/keyrings/google-keyring.gpg] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google-chrome.list' \
#    && apt update && apt install -y google-chrome-stable \
#    && pecl install apcu xdebug \
#    && docker-php-ext-install mysqli pdo_mysql opcache \
#    && docker-php-ext-enable apcu xdebug \

RUN apt-get -y update && apt-get -y upgrade \
    && apt-get -y install git unzip vim supervisor tmux wget gnupg2 libzip-dev \
    && pecl install apcu xdebug \
    && docker-php-ext-install mysqli pdo_mysql opcache zip \
    && docker-php-ext-enable apcu xdebug \
    && touch /tmp/xdebug.log \
    && chmod 777 /tmp/xdebug.log

COPY ./php.ini /usr/local/etc/php/php.ini
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
