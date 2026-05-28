FROM dunglas/frankenphp:1-alpine

# Set Caddy server name to serve on standard port 80 and disable automatic HTTPS
# inside the container (since your external proxy or Komodo deals with SSL certificates)
ENV SERVER_NAME=:80

# Use the official production PHP configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Install system utilities needed by Composer
RUN apk add --no-cache git unzip zip

# Install required PHP extensions for Laravel 
# Added `pdo_pgsql` for PostgreSQL support and `redis` for caching/queues
RUN install-php-extensions pdo_mysql pdo_pgsql gd zip intl opcache pcntl bcmath redis

# Copy Composer from the official composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 1. Copy composer files first to leverage Docker layer caching
COPY composer.json composer.lock ./

# 2. Install dependencies without running scripts (scripts will run after files are copied)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# 3. Copy the rest of your application code
COPY . /app

# 4. Generate optimized autoloader and run auto-discovery
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative \
    && php artisan package:discover --ansi

# 5. Correct file permissions for storage & cache so the web server can write to them
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R root:www-data storage bootstrap/cache

RUN php artisan storage:link

ENV DOCUMENT_ROOT=/app/public

EXPOSE 80