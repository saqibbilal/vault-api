# --- Stage 1: Build dependencies ---
FROM php:8.4-fpm-alpine AS builder

# Install Laravel essentials: pdo_pgsql, zip, opcache, and NOW gd
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql zip opcache gd

WORKDIR /var/www/html
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-scripts

# --- Stage 2: Final Production App Image ---
FROM php:8.4-fpm-alpine

# Re-install only the runtime libraries needed
RUN apk add --no-cache libpq

# Install the same extensions in the production stage
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql zip opcache gd

WORKDIR /var/www/html

# Copy only the necessary files from builder
COPY --from=builder /var/www/html /var/www/html

# Set production PHP settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
