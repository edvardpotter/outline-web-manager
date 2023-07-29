# Start with the official PHP 8.2 image
FROM php:8.2-fpm

# Install system dependencies and clean cache
RUN apt-get update && apt-get install -y \
 git \
 curl \
 libpng-dev \
 libonig-dev \
 libxml2-dev \
 zip \
 unzip

# Install PHP extensions
RUN docker-php-ext-install mbstring exif pcntl bcmath gd

# Copy Composer from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

RUN mkdir -p /var/www/var && \
    mkdir -p /var/www/vendor

RUN chown -R www-data:www-data /var/www/var && \
    chown -R www-data:www-data /var/www/vendor

# Change current user to www
USER www-data

COPY --chown=www-data:www-data \
    composer.* \
    symfony.lock ./

RUN composer install --no-dev --prefer-dist --no-progress --no-scripts --no-interaction --ignore-platform-reqs

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

RUN bin/console doctrine:migrations:migrate \
    && bin/console user:create user@email.local 123456
