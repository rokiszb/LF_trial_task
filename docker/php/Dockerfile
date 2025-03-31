FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libxpm-dev

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath zip intl xml gd


RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath zip intl xml

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN echo "upload_max_filesize = 20M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 21M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html

COPY . /var/www/html/

RUN composer install --no-interaction --optimize-autoloader

RUN usermod -a -G www-data www-data

RUN mkdir -p /var/www/html/public/uploads/news \
    && mkdir -p /var/www/html/public/media/cache \
    && chown -R www-data:www-data /var/www/html/public/uploads /var/www/html/public/media \
    && chmod -R 775 /var/www/html/public/uploads /var/www/html/public/media

RUN chown -R www-data:www-data /var/www/html/var

