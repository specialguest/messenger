# https://docs.docker.com/develop/develop-images/dockerfile_best-practices/
# https://www.digitalocean.com/community/tutorials/how-to-set-up-laravel-nginx-and-mysql-with-docker-compose
# https://www.exploit.cz/how-to-compile-amqp-extension-for-php-8-0-via-multistage-dockerfile/
FROM php:8.0-fpm-buster

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && apt-get install -y \
        git \
        curl \
        zip \
        unzip \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        zlib1g-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev \
        graphviz \
        supervisor \
        librabbitmq-dev \
    && docker-php-ext-configure gd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        gd \
        intl \
        pdo_mysql \
        mysqli \
        zip \
        opcache \
    #&& docker-php-ext-enable mysqli \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    # SUPERVISOR
    && touch /var/run/supervisor.sock \
    && mkdir -p /var/log/supervisor && touch /var/log/supervisor/supervisord.log \
    # APCU
    && pecl install apcu \
    && docker-php-ext-enable apcu

# AMQP - en attendant le supporte natif pecl/docker-php-ext-install
RUN docker-php-source extract \
    && mkdir -p /usr/src/php/ext/amqp \
    && curl -fsSL https://github.com/php-amqp/php-amqp/archive/master.tar.gz | tar xvz -C /usr/src/php/ext/amqp --strip-components=1 \
    && docker-php-ext-install amqp \
    && docker-php-source delete

# copy the Composer PHAR from the Composer image into the PHP image
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www

EXPOSE 9000

CMD /usr/bin/supervisord -c '/etc/supervisor/supervisord.conf' ; php-fpm


