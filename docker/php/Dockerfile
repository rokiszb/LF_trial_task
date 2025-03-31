FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl xml

RUN usermod -a -G www-data www-data \
    && groupadd -g 1000 hostuser || true \
    && useradd -u 1000 -g 1000 -s /bin/bash -m hostuser || true \
    && usermod -a -G www-data hostuser

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html/

RUN composer install --no-interaction --optimize-autoloader

RUN mkdir -p /var/www/html/public/uploads/news /var/www/html/public/media/cache \
    && chown -R www-data:www-data /var/www/html
