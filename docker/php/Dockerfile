FROM php:fpm

MAINTAINER Denis Sidorov <denis.sidorov@altarix.ru>

RUN apt-get update -q

RUN apt-get install -y libicu52 libicu-dev && docker-php-ext-install -j$(nproc) intl
RUN apt-get install -y libpq-dev && docker-php-ext-install -j$(nproc) pdo pdo_pgsql

RUN docker-php-ext-install -j$(nproc) \
    bcmath \
    exif \
    pcntl

RUN docker-php-ext-enable opcache

RUN apt-get install -y \
    vim \
    git

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/bin/composer

ADD php.ini /usr/local/etc/php/php.ini
ADD add_docker_group /usr/bin/add_docker_group

ADD ssh /var/www/.ssh
RUN chown www-data:www-data -R /var/www/.ssh && \
    chmod 700 /var/www/.ssh && \
    chmod 600 /var/www/.ssh/*

RUN chgrp www-data /var/www && \
    chmod g+rwX /var/www