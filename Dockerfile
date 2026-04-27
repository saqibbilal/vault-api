FROM php:8.4-cli-alpine

# Install system dependencies
RUN apk add --no-cache \
    libpq-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev

# Install PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql zip opcache intl gd

WORKDIR /var/www/html

# Get Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Ensure permissions are correct for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Generate the symlink for images
RUN php artisan storage:link --force

# Listen on the port Railway provides
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT} -t public"]
