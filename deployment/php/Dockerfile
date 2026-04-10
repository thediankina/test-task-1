FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd \
    --with-jpeg \
    --with-freetype \
    && docker-php-ext-install gd

RUN docker-php-ext-install opcache pdo pdo_mysql mysqli

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    && chmod 755 /usr/bin/composer

RUN a2enmod rewrite

RUN echo 'date.timezone = "Europe/Moscow"' >> /usr/local/etc/php/conf.d/timezone.ini;
RUN echo 'memory_limit = 256M' >> /usr/local/etc/php/conf.d/memory_limit.ini;

WORKDIR /src
