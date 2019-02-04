FROM php:7.1-apache

RUN apt-get update -y && apt-get install -y \
    curl \
    libcurl4-openssl-dev \
    git \
    libicu-dev \
    locales \
    unzip \
    zlib1g-dev

RUN docker-php-ext-install \
    curl \
    intl \
    pdo \
    pdo_mysql \
    zip

RUN echo "en_US.UTF-8 UTF-8" >> /etc/locale.gen
RUN locale-gen
RUN cp /usr/share/zoneinfo/Europe/Berlin /etc/localtime

COPY ./etc/conf.d/php.ini /usr/local/etc/php/conf.d/php.ini

RUN a2enmod rewrite
RUN service apache2 restart

RUN php -r "copy('https://getcomposer.org/composer.phar', '/usr/local/bin/composer');" \
    && chmod ugo+x /usr/local/bin/composer \
    && composer self-update
